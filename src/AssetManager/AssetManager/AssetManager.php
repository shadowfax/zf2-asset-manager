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
namespace AssetManager\AssetManager;

use Zend\Stdlib\SplStack;

use Zend\ServiceManager\ServiceManager;

use Zend\ServiceManager\ServiceManagerAwareInterface;

use AssetManager\Asset\Resolver\AssetPathStack;


use Zend\Mvc\Router\RouteMatch;

use Zend\Mvc\MvcEvent;




class AssetManager implements 
	ServiceManagerAwareInterface
{
	
    protected $services;
	
	protected $paths;
	
	protected $assetPathStack;
	
	public function __construct()
	{
		$this->paths = new SplStack();
		$this->assetPathStack = new AssetPathStack();
	}

    public function setServiceManager(ServiceManager $serviceManager)
    {
    	$this->services = $serviceManager;
    	return $this;
    }
    
    /*
    public function setRoute($route)
    {
    	$router = $this->services->get('Router');
    	
    	if ($router->hasRoute('asset_manager')) {
    		$router->removeRoute('asset_manager');
    	}
    	
    	$router->addRoute(
    		'asset_manager',
    		array(
    			'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => $route,
                ),
                'may_terminate' => true,
                'child_routes' => array(
                	'default' => array(
                		'type' => 'Wildcard'
                	)
                )
    		)
    	);
    }
    */	
	     
}