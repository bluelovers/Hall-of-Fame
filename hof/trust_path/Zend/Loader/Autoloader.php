<?php

/**
 * Minimal Zend_Loader_Autoloader stub
 * Original: Zend Framework 1.x (c) 2005-2012 Zend Technologies
 */
require_once 'Zend/Loader.php';

class Zend_Loader_Autoloader
{
    protected static $_instance;

    protected $_autoloaders = array();

    protected $_defaultAutoloader = array('Zend_Loader', 'loadClass');

    protected $_suppressNotFoundWarnings = false;

    protected $_namespaceAutoloaders = array();

    protected $_namespaces = array(
        'Zend_'  => true,
        'ZendX_' => true,
    );

    protected $_internalAutoloader;

    protected function __construct()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
        $this->_internalAutoloader = array($this, '_autoload');
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function resetInstance()
    {
        self::$_instance = null;
    }

    public static function autoload($class)
    {
        $self = self::getInstance();

        foreach ($self->getClassAutoloaders($class) as $autoloader) {
            if ($autoloader instanceof self) {
                if ($autoloader->autoload($class)) {
                    return $class;
                }
            } elseif (is_string($autoloader)) {
                if ($autoloader !== $class) {
                    $self->_internalAutoloader($class);
                }
            } else {
                call_user_func($autoloader, $class);
            }
        }

        try {
            call_user_func($self->_internalAutoloader, $class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function _autoload($class)
    {
        $callback = $this->getDefaultAutoloader();
        try {
            if ($this->suppressNotFoundWarnings()) {
                @call_user_func($callback, $class);
            } else {
                call_user_func($callback, $class);
            }
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getClassAutoloaders($class)
    {
        $autoloaders = $this->getAutoloaders();
        foreach ($this->_namespaceAutoloaders as $ns => $loaders) {
            $ns = (string) $ns;
            if ('' === $ns) {
                continue;
            }
            $nsWithSep = rtrim($ns, '_') . '_';
            if (0 === strpos($class, $nsWithSep)) {
                $autoloaders = array_merge($autoloaders, $loaders);
            }
        }
        return $autoloaders;
    }

    public function getNamespaceAutoloaders($namespace)
    {
        $namespace = (string) $namespace;
        if (!array_key_exists($namespace, $this->_namespaceAutoloaders)) {
            $this->_namespaceAutoloaders[$namespace] = array();
        }
        return $this->_namespaceAutoloaders[$namespace];
    }

    public function getRegisteredNamespaces()
    {
        return array_keys($this->_namespaces);
    }

    public function registerNamespace($namespace)
    {
        if ('' != $namespace && !isset($this->_namespaces[$namespace])) {
            $this->_namespaces[$namespace] = true;
        }
        return $this;
    }

    public function unregisterNamespace($namespace)
    {
        if (isset($this->_namespaces[$namespace])) {
            unset($this->_namespaces[$namespace]);
        }
        return $this;
    }

    public function suppressNotFoundWarnings($flag = null)
    {
        if (null !== $flag) {
            $this->_suppressNotFoundWarnings = (bool) $flag;
        }
        return $this->_suppressNotFoundWarnings;
    }

    public function getAutoloaders()
    {
        return $this->_autoloaders;
    }

    public function setAutoloaders(array $autoloaders)
    {
        $this->_autoloaders = $autoloaders;
        return $this;
    }

    public function getDefaultAutoloader()
    {
        return $this->_defaultAutoloader;
    }

    public function setDefaultAutoloader($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Invalid callback specified');
        }
        $this->_defaultAutoloader = $callback;
        return $this;
    }

    public function pushAutoloader($callback, $namespace = '')
    {
        $autoloaders = $this->getAutoloaders();
        array_push($autoloaders, $callback);
        $this->setAutoloaders($autoloaders);

        $namespace = (array) $namespace;
        foreach ($namespace as $ns) {
            $autoloaders = $this->getNamespaceAutoloaders($ns);
            array_push($autoloaders, $callback);
            $this->_setNamespaceAutoloaders($autoloaders, $ns);
        }
        return $this;
    }

    public function unshiftAutoloader($callback, $namespace = '')
    {
        $autoloaders = $this->getAutoloaders();
        array_unshift($autoloaders, $callback);
        $this->setAutoloaders($autoloaders);

        $namespace = (array) $namespace;
        foreach ($namespace as $ns) {
            $autoloaders = $this->getNamespaceAutoloaders($ns);
            array_unshift($autoloaders, $callback);
            $this->_setNamespaceAutoloaders($autoloaders, $ns);
        }
        return $this;
    }

    public function removeAutoloader($callback, $namespace = null)
    {
        if (null === $namespace) {
            $autoloaders = $this->getAutoloaders();
            if (false !== ($index = array_search($callback, $autoloaders, true))) {
                unset($autoloaders[$index]);
                $this->setAutoloaders($autoloaders);
            }
            foreach ($this->_namespaceAutoloaders as $ns => $autoloaders) {
                if (false !== ($index = array_search($callback, $autoloaders, true))) {
                    unset($autoloaders[$index]);
                    $this->_setNamespaceAutoloaders($autoloaders, $ns);
                }
            }
        } else {
            $namespace = (array) $namespace;
            foreach ($namespace as $ns) {
                $autoloaders = $this->getNamespaceAutoloaders($ns);
                if (false !== ($index = array_search($callback, $autoloaders, true))) {
                    unset($autoloaders[$index]);
                    $this->_setNamespaceAutoloaders($autoloaders, $ns);
                }
            }
        }
        return $this;
    }

    protected function _setNamespaceAutoloaders(array $autoloaders, $namespace = '')
    {
        $namespace = (string) $namespace;
        $this->_namespaceAutoloaders[$namespace] = $autoloaders;
        return $this;
    }
}
