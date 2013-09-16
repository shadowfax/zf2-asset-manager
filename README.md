zf2-asset-manager
=================

Yet another Zend Framework 2 asset manager. 

If you are looking for a really simple and flexible asset manager for your application 
this could be it; but probably you are looking for another project.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)

Installation
------------

### Main Setup

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "shadowfax/zf2-asset-manager": "dev-master"
    }
    ```

2. Now tell composer to download ThemeManager by running the command:

    ```bash
    $ php composer.phar update
    ```

#### By cloning project

1. Clone this project into your `./vendor/` directory.

### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            'ThemeManager',
            // ...
        ),
        // ...
    );
    ```

Usage
-----

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

This can also be done through the `module.config.php` file:

    return array(
        // ...
        'asset_manager' => array(
            'paths' => array(
                __DIR__ . '/../assets'
            )
        )
    );

By default the assets route is `/assets`, but this can be changed through the configuration
files.

    return array(
        // ...
        'asset_manager' => array(
            'routes' => array(
                'files' => array(
                    'type' => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/files',
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'default' => array(
                            'type' => 'Wildcard'
                        )
                    )
                )
            ),
        )
    );
    
In this example we have changed the route for assets from `assets` to `/files`. We suggest this 
changes are made in a global configuration file such as `config/autoload/assetmanager.global.php`
as doing this on a `module.config.php` could bring errors due to route collisions and the
`AssetManager` does NOT consider modules independantly.

Multiple routes may be created. For example:

    return array(
        // ...
        'asset_manager' => array(
            'routes' => array(
                'css' => array(
                    'type' => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/css',
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'default' => array(
                            'type' => 'Wildcard'
                        )
                    )
                ),
                'js' => array(
                    'type' => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/js',
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'default' => array(
                            'type' => 'Wildcard'
                        )
                    )
                ),
                'images' => array(
                    'type' => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/images',
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'default' => array(
                            'type' => 'Wildcard'
                        )
                    )
                )
            ),
        )
    );

This allows us to set a transparent configuration of the asset manager.

You don't have to worry about route name collisions as the `AssetManager` will prepend
`asset_manager` to the route name. In the example above the route name `css` will be converted
to `asset_manager/css` before it gets added to the router.
