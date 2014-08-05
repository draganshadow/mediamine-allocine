<?php
namespace MediaMine\Module\Allocine;
use MediaMine\Module\Allocine\Service\InstallService;
use MediaMine\Module\Allocine\Tunnel\Allocine\AllocineTunnel;

return array(
    'factories' => array(
        'MediaMine\Module\Allocine\Service\Install' => function ($sm) {
                $service = new InstallService();
                return $service;
            },
        'AllocineTunnel' => function ($sm) {
                $tunnel = new AllocineTunnel();
                $tunnel->setLogger($sm->get('mediamine-tunnel-log'));
                return $tunnel;
            },
        'AlloHelper' => function ($sm) {
                $helper = new \AlloHelper();
                $helper->setUtf8Decode(true);
                return $helper;
            },
    ),
);
