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
namespace AssetManager\Asset\Resolver;

interface ResolverInterface
{
    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string $name
     * @return mixed
     */
    public function resolve($name);
}