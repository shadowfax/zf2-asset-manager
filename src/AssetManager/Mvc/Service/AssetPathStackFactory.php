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

use AssetManager\Asset\Resolver\AssetPathStack;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ServiceManager\FactoryInterface;

class AssetPathStackFactory implements FactoryInterface
{

	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$assetPathStack = new AssetPathStack();
		return $assetPathStack;
	}
}