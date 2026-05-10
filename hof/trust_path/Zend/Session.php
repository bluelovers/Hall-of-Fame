<?php

/**
 * Zend_Session stub for HOF
 * Minimal ZF1-compatible session wrapper
 */
class Zend_Session
{
    protected static $_started = false;
    protected static $_options = array();
    protected static $_rememberMe = false;
    protected static $_rememberSeconds = 86400;

    public static function start()
    {
        if (!self::$_started)
        {
            self::_setOptions();
            session_start();
            self::$_started = true;
        }
    }

    public static function setId($id)
    {
        if (!self::$_started)
        {
            session_id($id);
        }
    }

    public static function getId()
    {
        return session_id();
    }

    public static function getOptions($optionName = null)
    {
        if ($optionName === null)
        {
            return self::$_options;
        }
        return isset(self::$_options[$optionName]) ? self::$_options[$optionName] : null;
    }

    public static function setOptions(array $userOptions = array())
    {
        foreach ($userOptions as $k => $v)
        {
            self::$_options[$k] = $v;
        }
        if (self::$_started)
        {
            self::_setOptions();
        }
    }

    protected static function _setOptions()
    {
        foreach (self::$_options as $k => $v)
        {
            ini_set('session.' . $k, $v);
        }
    }

    public static function destroy()
    {
        if (self::$_started)
        {
            session_destroy();
            self::$_started = false;
        }
    }

    public static function forgetMe()
    {
        self::$_rememberMe = false;
    }

    public static function rememberUntil($seconds = 0)
    {
        self::$_rememberMe = true;
        if ($seconds > 0)
        {
            self::$_rememberSeconds = (int)$seconds;
        }
        $lifetime = time() + self::$_rememberSeconds;
        setcookie(session_name(), session_id(), $lifetime, '/');
    }

    public static function regenerateId()
    {
        if (self::$_started)
        {
            session_regenerate_id(true);
        }
    }

    public static function stop()
    {
        session_write_close();
        self::$_started = false;
    }

    public static function namespaceIsset($namespace, $key)
    {
        return isset($_SESSION[$namespace][$key]);
    }

    public static function namespaceUnset($namespace, $key)
    {
        if (isset($_SESSION[$namespace][$key]))
        {
            unset($_SESSION[$namespace][$key]);
        }
    }

    public static function namespaceGet($namespace)
    {
        return isset($_SESSION[$namespace]) ? $_SESSION[$namespace] : null;
    }
}
