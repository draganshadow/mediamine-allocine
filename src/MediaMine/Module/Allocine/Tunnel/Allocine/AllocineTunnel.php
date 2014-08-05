<?php
namespace MediaMine\Module\Allocine\Tunnel\Allocine;

use Doctrine\ORM\Query;
use MediaMine\Core\Entity\System\Task;
use MediaMine\Core\Entity\Tunnel\Person;
use MediaMine\Core\Tunnel\AbstractTunnel;
use MediaMine\Core\Tunnel\Abilities\PersonImport;

class AllocineTunnel extends AbstractTunnel implements PersonImport
{
    const KEY = 'allocine';

    protected $options = array();

    /**
     * Return tunnel name
     * @return string
     */
    public function getTunnelName()
    {
        return self::KEY;
    }

    /**
     * Return array of handled entities and fields
     * @return array
     */
    public function getAbilities() {
        return array(
            'Person' => array()
        );
    }

    public function enableTunnel() {
        $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');

        $result = $cronRepository->findBy(array('key' => 'AllocineTunnelCheckData'));
        if (!count($result)) {
            return array('error' => 1);
        }
        $cron = $result[0];
        $cron->active = true;
        $this->getEntityManager()->persist($cron);
        $this->batch(1);
    }

    public function disableTunnel() {
        $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');

        $result = $cronRepository->findBy(array('key' => 'AllocineTunnelCheckData'));
        if (!count($result)) {
            return array('error' => 1);
        }
        $cron = $result[0];
        $cron->active = false;
        $this->getEntityManager()->persist($cron);
        $this->batch(1);
    }

    public function checkData() {

        $tq = $this->getEntityManager()->createQueryBuilder();
        $nbtask = $tq->select('COUNT(Task)')
            ->from('MediaMine\Core\Entity\System\Task','Task')
            ->where('Task.groupKey = \'allocine\'')
            ->where('Task.key = \'person\'')
            ->getQuery()
            ->getSingleScalarResult();
        if ($nbtask == 0) {
            $params = array();

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('Person')
                ->from('MediaMine\Core\Entity\Common\Person','Person');
            $qb->where('Person.id  NOT IN ' .
                '(SELECT p.id FROM MediaMine\Core\Entity\Tunnel\Person AS tp JOIN tp.person p JOIN tp.tunnel t WHERE t.key = \'allocine\')');
            $q = $qb->setParameters($params)->getQuery();

            $iterableResult = $q->iterate();
            $this->createTasksPool('person', $iterableResult, 'AllocineTunnel', 120);
        }
        $this->getEntityManager()->flush();
    }

    public function processTask($id) {
        /**
         * @var $task \MediaMine\Core\Entity\System\Task
         */
        $task = $this->getRepository('System\Task')->find($id);
        if ($task) {
            if ($task->key == 'person') {
                $this->importPerson($task->reference);
            }
            $this->getEntityManager()->remove($task);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function importPerson($id, $update = false) {
        $this->loadOptions();
        $tunnel = $this->getRepository('System\Tunnel')->findFullByKey($this->getTunnelName());

        $tp = $this->getRepository('Tunnel\Person')->findFullBy(array(
            'id' => $id,
            'tunnel' => $tunnel->id
        ));
        $create = count($tp) == 0;
        $person = false;
        if ($update || $create) {
            $personRepo = $this->getRepository('Common\Person');
            $sourcePerson = $personRepo->find($id);
            $alloHelper = $this->getServiceLocator()->get('AlloHelper');
            $result = $alloHelper->search($sourcePerson->name, 1, 10, false, array('person'));
            if ($result->totalResults) {
                $persons = $result->person->getArray();
                if (count($persons) > 0) {
                    $result = $alloHelper->person($persons[0]['code'], 'medium')->getArray();
                    if (array_key_exists('picture', $result)) {
                        $ext = substr($result['picture']['href'], strrpos($result['picture']['href'], '.'));
                        $path = $this->options['imagePath'][0] . '/' .
                            str_replace(' ', '-', $result['name']['given'] . '_' . $result['name']['family']) . $ext;
                        file_put_contents($path, fopen($result['picture']['href'], 'r'));
                        $this->getServiceLocator()->get('File')->scanPath($this->options['imagePath'][0]);
                        $f = $this->getRepository('File\File')->findFullBy(array('pathKey' => md5(realpath($path))));
                    } else {
                        $f = null;
                    }

                    $person = $create ? new Person() : $tp[0];
                    $biography = array_key_exists('biography', $result) ?
                        strip_tags(str_replace('<br />', PHP_EOL, $result['biography']))
                        : '';
                    $biography = utf8_encode($biography);
                    $person->exchangeArray(array(
                        'tunnel' => $tunnel,
                        'person' => $sourcePerson,
                        'name' => $result['name']['given'] . ' ' . $result['name']['family'],
                        'firstName' => $result['name']['given'],
                        'lastName' => $result['name']['family'],
                        'country' => array_key_exists('nationality', $result) ? $result['nationality'][0]['$'] : null,
                        'birthDate' => array_key_exists('birthDate', $result) ? new \DateTime($result['birthDate']): null,
                        'deathDate' => array_key_exists('deathDate', $result) ? new \DateTime($result['deathDate']) : null,
                        'summary' => $biography,
                        'images' => $f,
                        'raw' => json_encode($result),
                    ));
                    $this->getEntityManager()->persist($person);
                }
            } else {
                if ($create) {
                    $person = new Person();
                    $person->exchangeArray(array(
                        'person' => $sourcePerson,
                        'tunnel' => $tunnel,
                        'name' => $sourcePerson->name,
                        'rid' => $id,
                        'raw' => null,
                    ));
                    $this->getEntityManager()->persist($person);
                } else {
                    $person =  $tp[0];
                }
            }
        }

        $this->getEntityManager()->flush();
        return $person;
    }

    public function loadOptions() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')
            ->from('MediaMine\Core\Entity\System\Setting','s')
            ->where('s.groupKey = \'allocine\'');
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $this->options[$result->key] = $result->value;
        }
    }
}
