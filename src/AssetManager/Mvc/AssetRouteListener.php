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
namespace AssetManager\Mvc;


use AssetManager\Asset\Resolver\MimeResolver;

use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventManagerInterface;

use Zend\EventManager\ListenerAggregateInterface;

use Zend\Mvc\Router;


class AssetRouteListener implements ListenerAggregateInterface
{
	/**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
	/**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
    
    /**
     * Listen to the "route" event and determine if an asset should be loaded.
     *
     * @param  MvcEvent $e
     * @return Response|null
     */
    public function onRoute(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof Router\RouteMatch) {
            // Can't do anything without a route match
            return;
        }
        
    	// Check for 'asset_manager' route
    	$routeName = $matches->getMatchedRouteName();
    	$routeName = explode('/', $routeName, 2);
    	$routeName = $routeName[0];
    	
    	if ($routeName !== 'asset_manager') {
    		// Not an asset request
    		return;
    	}
    	
    	// TODO: Take care of grouped assets (Merged assets)
    	//       Right now only direct access
    	
    	$serviceManager = $e->getApplication()->getServiceManager();
    	$router         = $e->getRouter();
    	$assetManager   = $serviceManager->get('AssetManager');
    	
    	$requestPath     = $e->getRequest()->getUri()->getPath();
    	$requestBasePath = $router->assemble(array(), array('name' => 'asset_manager'));
    	$requestBasePath = $requestBasePath . '/';
   
    	if (strlen($requestPath) <= strlen($requestBasePath)) {
    		// Not enough data!
    		return;
    	}
    	
    	$requestPath = substr($requestPath, strlen($requestBasePath) - 1);
    	
    	$assetPathStack = $serviceManager->get('AssetPathStack');
    	$content = $assetPathStack->resolve($requestPath);  	
    	if (!empty($content)) {
    		$response = $e->getResponse();
    		$headers  = $response->getHeaders();
    		$headers->addHeaderLine('Content-Type', MimeResolver::getMimeType($requestPath));
    		$response->setContent($content);
    		return $response;
    	}
    	
    }
    
    
	
}