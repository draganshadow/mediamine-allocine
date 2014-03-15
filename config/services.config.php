<?php
namespace MediaMine\Module\Allocine;
use MediaMine\Module\Allocine\Tunnel\Allocine\AllocineTunnel;

return array(
    'factories' => array(
        'AllocineTunnel' => function ($sm) {
                $tunnel = new AllocineTunnel();
                $tunnel->setLogger($sm->get('mediamine-tunnel-log'));
                return $tunnel;
            },
        'AlloHelper' => function ($sm) {
                $helper = new \AlloHelper();
                return $helper;
            },
    ),
);
