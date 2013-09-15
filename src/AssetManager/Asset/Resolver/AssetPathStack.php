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

use SplFileInfo;
use Traversable;
use Zend\Stdlib\SplStack;
use AssetManager\Asset\Exception;

/**
 * Resolves view scripts based on a stack of paths
 */
class AssetPathStack implements ResolverInterface
{
    
    /**
     * @var SplStack
     */
    protected $paths;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $options
     */
    public function __construct($options = null)
    {
        $this->paths = new SplStack;
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure object
     *
     * @param  array|Traversable $options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable object; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'script_paths':
                    $this->addPaths($value);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Add many paths to the stack at once
     *
     * @param  array $paths
     * @return TemplatePathStack
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
        return $this;
    }

    /**
     * Rest the path stack to the paths provided
     *
     * @param  SplStack|array $paths
     * @return TemplatePathStack
     * @throws Exception\InvalidArgumentException
     */
    public function setPaths($paths)
    {
        if ($paths instanceof SplStack) {
            $this->paths = $paths;
        } elseif (is_array($paths)) {
            $this->clearPaths();
            $this->addPaths($paths);
        } else {
            throw new Exception\InvalidArgumentException(
                "Invalid argument provided for \$paths, expecting either an array or SplStack object"
            );
        }

        return $this;
    }

    /**
     * Normalize a path for insertion in the stack
     *
     * @param  string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        $path .= DIRECTORY_SEPARATOR;
        return $path;
    }

    /**
     * Add a single path to the stack
     *
     * @param  string $path
     * @return TemplatePathStack
     * @throws Exception\InvalidArgumentException
     */
    public function addPath($path)
    {
        if (!is_string($path)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid path provided; must be a string, received %s',
                gettype($path)
            ));
        }
        $this->paths[] = static::normalizePath($path);
        return $this;
    }

    /**
     * Clear all paths
     *
     * @return void
     */
    public function clearPaths()
    {
        $this->paths = new SplStack;
    }

    /**
     * Returns stack of paths
     *
     * @return SplStack
     */
    public function getPaths()
    {
        return $this->paths;
    }

    protected function canAppend($filename)
    {
    	$extension = pathinfo($filename, PATHINFO_EXTENSION);
    	$extension = strtolower($extension);
    	
    	if (($extension === 'css') || ($extension === 'js')) return true;
    	return false;
    }

    /**
     * Retrieve the filesystem path to a view script
     *
     * @param  string $name
     * @param  null|Renderer $renderer
     * @return string
     * @throws Exception\DomainException
     */
    public function resolve($name)
    {
    	if (preg_match('#\.\.[\\\/]#', $name)) {
            throw new Exception\DomainException(
                'Requested asset may not include parent directory traversal ("../", "..\\" notation)'
            );
        }
        
    	// Check if we have paths
        if (!count($this->paths)) {
            //$this->lastLookupFailure = static::FAILURE_NO_PATHS;
            return null;
        }
        
        // TODO: Check cache!!!
        
        $files = array();
        foreach ($this->paths as $path) {
            $file = new SplFileInfo($path . $name);
            if ($file->isReadable()) {
                // Found! Return it.
                if (($filePath = $file->getRealPath()) === false && substr($path, 0, 7) === 'phar://') {
                    // Do not try to expand phar paths (realpath + phars == fail)
                    $filePath = $path . $name;
                    if (!file_exists($filePath)) {
                        break;
                    }
                }
                
                $files[] = $filePath;
            }
        }

        $count = count($files);
        if ($count > 1) {
        	$canAppend = $this->canAppend($name);
        	if ($canAppend) {
        		$content = '';
        		foreach ($files as $file) {
        			$fileContent = file_get_contents($file);
        			if ($fileContents) {
        				$content .= $fileContent;
        			}
        		}
        		return $content;
        	} else {
        		// TODO: Create an exception
        		throw new \Exception("Too many assets found.");
        	}
        } elseif($count === 1) {
        	$content = file_get_contents($files[0]);
        	if ($content) {
        		return $content;
        	}
        }
        
        return null;
    }

}
