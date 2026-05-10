<?php

/**
 * Zend_Session_Namespace stub for HOF
 * Minimal ZF1-compatible session namespace container
 */
class Zend_Session_Namespace
{
    protected $_namespace = 'Default';

    public function __construct($namespace = 'Default', $singleInstance = false)
    {
        $this->_namespace = (string)$namespace;
        if (!isset($_SESSION[$this->_namespace]))
        {
            $_SESSION[$this->_namespace] = array();
        }
    }

    public function __get($key)
    {
        if (isset($_SESSION[$this->_namespace][$key]))
        {
            return $_SESSION[$this->_namespace][$key];
        }
        return null;
    }

    public function __set($key, $value)
    {
        $_SESSION[$this->_namespace][$key] = $value;
    }

    public function __isset($key)
    {
        return isset($_SESSION[$this->_namespace][$key]);
    }

    public function __unset($key)
    {
        if (isset($_SESSION[$this->_namespace][$key]))
        {
            unset($_SESSION[$this->_namespace][$key]);
        }
    }

    public function unsetAll()
    {
        $_SESSION[$this->_namespace] = array();
    }
}
