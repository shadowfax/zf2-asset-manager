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
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onPreRoute'), 10000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onPostRoute'), -10000);
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
     * Late-binding asset routes.
     * 
     * @param MvcEvent $event
     */
    public function onPreRoute(MvcEvent $event)
    {
    	// Get the router object
    	$serviceManager = $event->getApplication()->getServiceManager();
    	$router         = $event->getRouter();
    	
    	// Load the route configuration
    	$config = $serviceManager->get('Config');
	    $config = isset($config['asset_manager']) && (is_array($config['asset_manager']) || $config['asset_manager'] instanceof ArrayAccess)
	              ? $config['asset_manager']
	              : array();
	    $config = isset($config['routes']) && (is_array($config['routes']) || $config['routes'] instanceof ArrayAccess)
	              ? $config['routes']
	              : array();
	    
	    // There can be special routes defined:
	    //    /css
	    //    /js
	    //    ...
	    // This way the asset manager can be hidden
	    if (!empty($config)) {
	    	// Rename all routes so I get an easy base route 
	    	// name called 'asset_manager'
		    $keys   = array_keys($config);
		    array_walk($keys, function($n) {
		    	$n = 'asset_manager/' . $n;
		    });
		    $config = array_combine($keys, array_values($config));
		    
		    // Add the routes
    		$router->addRoutes($config);
	    } else {
	    	// No routes have been configured!
	    	// So I need to create my own
	    	$router->addRoute(
    			'asset_manager',
	    		array(
	    			'type' => 'Zend\Mvc\Router\Http\Literal',
	                'options' => array(
	                    'route'    => '/assets',
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
    }
    
    /**
     * Listen to the "route" event and determine if an asset should be loaded.
     *
     * @param  MvcEvent $e
     * @return Response|null
     */
    public function onPostRoute(MvcEvent $e)
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