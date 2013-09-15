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
namespace AssetManager\Mvc\Service;

use AssetManager\AssetManager\AssetManager;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ServiceManager\FactoryInterface;

class AssetManagerFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$assetManager = new AssetManager();
		$assetManager->setServiceManager($serviceLocator);
		$assetManager->setRoute('/assets');
		return $assetManager;
	}
}