<?php
namespace MediaMine\Module\Allocine;

return array(
    'mediamine' => array(
        'modules' => array(
            'allocine' => array(
                'module' => array(
                    'key' => 'allocine',
                    'namespace' => __NAMESPACE__,
                    'name' => 'Allocine',
                    'version' => '0.1',
                ),
                'tunnels' => array(
                    'allocine' => array(
                        'key' => 'allocine',
                        'service' => 'AllocineTunnel'
                    )
                ),
                'crons' => array(
                    array('key' => 'AllocineTunnelCheckData',
                        'frequency' => '0 */12 * * *',
                        'service' => 'AllocineTunnel',
                        'callback' => 'checkData',
                        'arguments' => array(),
                        'active' => false
                    ),
                    array('key' => 'AllocineTunnelProcessTasks',
                        'frequency' => '*/15 * * * *',
                        'service' => 'AllocineTunnel',
                        'callback' => 'processTasks',
                        'arguments' => array(),
                        'active' => false
                    ),
                ),
                'settings' => array(
                    'allocine' => array(
                        'imagePath' => array('data/module/allocine/images')
                    )
                )
            )
        ),
    ),
);
