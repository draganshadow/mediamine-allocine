<?php
namespace MediaMine\Module\Allocine;

return array(
    'mediamine' => array(
        'modules' => array(
            'allocine' => array(
                'key' => 'allocine',
                'namespace' => __NAMESPACE__,
                'name' => 'Allocine',
                'version' => '0.1',
                'tunnels' => array(
                    'allocine' => array(
                        'key' => 'allocine',
                        'service' => 'AllocineTunnel',
                        'cron' => array(

                        )
                    )
                ),
                'settings' => array()
            )
        ),
    ),
);
