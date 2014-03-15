<?php
namespace MediaMine\Module\Allocine\Tunnel\Allocine;

use MediaMine\Core\Entity\Tunnel\Person;
use MediaMine\Core\Tunnel\AbstractTunnel;
use MediaMine\Core\Tunnel\PersonImport;

class AllocineTunnel extends AbstractTunnel implements PersonImport
{
    const ALLOCINE = 'allocine';

    /**
     * Return tunnel name
     * @return string
     */
    function getTunnelName()
    {
        return self::ALLOCINE;
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

    /**
     * {@inheritdoc}
     */
    public function importPerson($id) {
        $personRepo = $this->getRepository('Common\Person');
        $sourcePerson = $personRepo->find($id);
        $alloHelper = new \AlloHelper();
        $result = $alloHelper->search($sourcePerson->name, 1, 10, false, array('person'));
        if ($result->count()) {
            $persons = $result->person->getArray();
            if (count($persons) > 0) {
                $result = $alloHelper->person($persons[0]['code'], 'medium')->getArray();
                $person = new Person();
                $person->exchangeArray(array(
                    'tunnel' => self::ALLOCINE,
                    'rid' => 0,
                    'name' => $result['name']['given'] . ' ' . $result['name']['family'],
                    'firstName' => $result['name']['given'],
                    'lastName' => $result['name']['family'],
                    'country' => $result['nationality']['$'],
                    'birthDate' => new \DateTime($result['birthDate']),
                    'deathDate' => $result['deathDate'] ? new \DateTime($result['deathDate']) : null,
                    'summary' => $result['biography'],
                    'images' => null,
                    'raw' => json_encode($result),
                ));
                return $person;
            }
        }
        return false;
    }
}
