<?php
namespace MediaMine\Allocine;

use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Adapter\ConsoleAdapter;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    const NAME    = 'MediaMine\Allocine';

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }


    public function getServiceConfig()
    {
        return include __DIR__ . '/../../../config/services.config.php';
    }

    /**
     * This method is defined in ConsoleBannerProviderInterface
     *
     * @param AdapterInterface $console
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console){
        return self::NAME;
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'cron',
            'cron'  => 'execute all crons',
        );
    }
}
