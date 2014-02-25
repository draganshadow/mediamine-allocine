<?php

namespace MediaMine\Module\Allocine;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        $config = array();

        $configFiles = array(
            __DIR__ . '/../../../../config/module.config.php',
        );

        // Merge all module config options
        foreach ($configFiles as $configFile) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
        }

        return $config;
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../../../../config/services.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'MediaMine' => __DIR__ . '/src/MediaMine',
                ),
            ),
        );
    }
}
