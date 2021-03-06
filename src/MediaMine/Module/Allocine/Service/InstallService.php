<?php
namespace MediaMine\Module\Allocine\Service;

use Doctrine\ORM\Query;
use MediaMine\Core\Service\AbstractService;

class InstallService extends AbstractService
{
    protected $defaultSettings = array(
        'allocine' => array(
            'imagePath' => array('data/module/allocine/images')
        )
    );

    protected $defaultCrons = array(
        array('key' => 'AllocineTunnelCheckData',
            'frequency' => '0 */12 * * *',
            'service' => 'AllocineTunnel',
            'callback' => 'checkData',
            'arguments' => array(),
            'active' => false
        )
    );

    public function install() {
        //TODO create images folder
        mkdir('data/module/allocine/images', 0777, true);
//
//        $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');
//        foreach ($this->defaultCrons as $c) {
//            $cronRepository->create($c);
//        }
//
//        $settingRepository = $this->getRepository('System\Setting');
//        foreach ($this->defaultSettings as $g => $ops) {
//            foreach ($ops as $k => $v) {
//                $settingRepository->create(array(
//                    'groupKey' => $g,
//                    'key'   => $k,
//                    'value' => $v
//                ));
//            }
//        }
//        $this->batch(1);
    }
}