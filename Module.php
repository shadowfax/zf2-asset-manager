<?php
/**
 * ZF2-AssetManager
 * 
 * Asset Manager for Zend Framework 2
 * 
 * @author    Juan Pedro Gonzalez
 * @copyright Copyright (c) 2013 Juan Pedro Gonzalez
 * @link      http://github.com/shadowfax/zf2-asset-manager
 * @license   http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace AssetManager;

use Zend\ServiceManager\ServiceLocatorInterface;

use AssetManager\Mvc\AssetRouteListener;

use Zend\ModuleManager\ModuleEvent;

use Zend\ModuleManager\ModuleManagerInterface;

use Zend\ModuleManager\Feature\InitProviderInterface;

use Zend\EventManager\EventInterface;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\Mvc\MvcEvent;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;

use Zend\ModuleManager\Feature\ServiceProviderInterface;

use Zend\Console\Console;

class Module implements 
	AutoloaderProviderInterface,
	ServiceProviderInterface, 
	BootstrapListenerInterface
{

	public function onBootstrap(EventInterface $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceManager      = $e->getApplication()->getServiceManager();
        
        // Listener have only sense when request is via http.
        if (!Console::isConsole()) {
        	$eventManager->attach(new AssetRouteListener());
        }
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
        return array(
            'factories' => array(
            	'AssetManager'               => 'AssetManager\Mvc\Service\AssetManagerFactory',
        		'AssetPathStack'             => 'AssetManager\Mvc\Service\AssetPathStackFactory',
            )
        );
    }
}