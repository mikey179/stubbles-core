<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Represents the root path within a project.
 *
 * The root path is defined as the path in which the whole application resides.
 * In case the application is inside a phar, it's the directory where the phar
 * is stored.
 *
 * @since  4.0.0
 * @Singleton
 */
class Rootpath
{
    /**
     * root path of application
     *
     * @type  string
     */
    private $rootpath;

    /**
     * constructor
     *
     * If no root path is given it tries to detect it automatically.
     *
     * @param   string  $rootpath  optional  path to root
     * @throws  IllegalArgumentException  in case a root path is given but does not exist
     */
    public function __construct($rootpath = null)
    {
        if (null !== $rootpath && !file_exists($rootpath)) {
            throw new IllegalArgumentException('Given rootpath "' . $rootpath . '" does not exist');
        }

        $this->rootpath = (null === $rootpath) ? ($this->detectRootPath()) : (realpath($rootpath));
    }

    /**
     * casts given value to an instance of Rootpath
     *
     * @param   string|Rootpath  $rootpath
     * @return  Rootpath
     */
    public static function castFrom($rootpath)
    {
        if ($rootpath instanceof self) {
            return $rootpath;
        }

        return new self($rootpath);
    }

    /**
     * returns root path
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->rootpath;
    }

    /**
     * returns absolute path to given local path
     *
     * Supports arbitrary lists of arguments, e.g.
     * <code>$rootpath->to('src', 'main', 'php', 'Example.php')</code>
     * will return "path/to/root/src/main/php/Example.php".
     *
     * @param   string...  $pathParts
     * @return  string
     */
    public function to()
    {
        return $this->rootpath . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, func_get_args());
    }

    /**
     * checks if given path is located within root path
     *
     * @param   string  $path
     * @return  bool
     */
    public function contains($path)
    {
        $realpath = realpath($path);
        if (false === $realpath) {
            return false;
        }

        return substr($realpath, 0, strlen($this->rootpath)) === $this->rootpath;
    }

    /**
     * returns list of source pathes defined for autoloader
     *
     * Relies on autoloader generated by Composer. If no such autoloader is
     * present the list of source pathes will be empty.
     *
     * @return  string[]
     */
    public function sourcePathes()
    {
        $vendorPathes = [];
        foreach (array_merge($this->loadPsr0Pathes(), $this->loadPsr4Pathes()) as $pathes) {
            if (is_array($pathes)) {
                $vendorPathes = array_merge($vendorPathes, $pathes);
            } else {
                $vendorPathes[] = $pathes;
            }
        }

        return $vendorPathes;
    }

    /**
     * loads list of pathes defined via PSR-0
     *
     * @return  string[]
     */
    private function loadPsr0Pathes()
    {
        if (file_exists($this->rootpath . '/vendor/composer/autoload_namespaces.php')) {
            return require $this->rootpath . '/vendor/composer/autoload_namespaces.php';
        }

        return [];
    }

    /**
     * loads list of pathes defined via PSR-4
     *
     * @return  string[]
     */
    private function loadPsr4Pathes()
    {
        if (file_exists($this->rootpath . '/vendor/composer/autoload_psr4.php')) {
            return require $this->rootpath . '/vendor/composer/autoload_psr4.php';
        }

        return [];
    }

    /**
     * detects root path
     *
     * @return  string
     */
    private function detectRootPath()
    {
        static $rootpath = null;
        if (null === $rootpath) {
            if (\Phar::running() !== '') {
                $rootpath = dirname(\Phar::running(false));
            } elseif (file_exists(__DIR__ . '/../../../../../../autoload.php')) {
                // stubbles/core is inside the vendor dir of the application
                // it is a dependency of
                $rootpath = realpath(__DIR__ . '/../../../../../../../');
            } else {
                // local checkout while development
                $rootpath = realpath(__DIR__ . '/../../../../');
            }
        }

        return $rootpath;
    }
}

