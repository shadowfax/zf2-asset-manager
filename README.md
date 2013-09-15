zf2-asset-manager
=================

Yet another Zend Framework 2 asset manager. 

If you are looking for a really simple and flexible asset manager for your application 
this could be it; but probably you are looking for another project.

This asset manager was written in order to support my ThemeManager. I just needed something
simple and flexible enough so I could change the assets on the fly. In my case, if I change
a theme, the AssetManager should be told to change the assets it is using.

This changes are done through the service `AssetPathStack` where we can add new paths for 
asset files, clear all paths, set new paths, etc... Lets look at an example inside a module 
(`Module.php` file):

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $assetPathStack = $serviceManager->get('AssetPathStack');
        $assetPathStack->addPath( __DIR__ . '/assets');
    }
    
With this code we are telling the `AssetPathStack` to add a new path to the `assets` folder
contained in our module. Now the assets inside our module will be accessed through the 
assets route.

By default the assets route is `/assets/*`, but this can be changed calling the `AssetManager` 
service. Lets expand the previous example a little:

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $assetManager   = $serviceManager->get('AssetManager');
        $assetManager->setRoute('/files');
        $assetPathStack = $serviceManager->get('AssetPathStack');
        $assetPathStack->addPath( __DIR__ . '/assets');
    }
    
In this example we have changed the route for assets to `/files` instead of `/assets`.

The route name for assets, in case you need to use it, is called `asset_manager`.

In a standalone installation we could create a global configuration file in autoload such 
as `assetmanager.global.php` or tweak or `module.config.php` file. A sample configuration 
could be:

    return array(
        'route' => '/assets'.
        'paths' => array(
            __DIR__ . '/../assets'
        )
    );
    
This sample is a `module.config.php` where we are setting the route and a path for the assets.

The `route` key is optional as it defaults to `/assets`; however it should only bre present at
a single point as only one route can be defined for assets.

The `paths` key may be present more than once and each entry will add an additional path to 
assets.

Imagine the previous configuration is for the `Application` module. We now want the `Album` 
module to have its own self-contained assets. The configuration for the Album module would be:

    return array(
        'paths' => array(
            __DIR__ . '/../assets'
        )
    );
    
As easy as that.
