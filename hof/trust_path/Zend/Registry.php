<?php

/**
 * Minimal Zend_Registry stub
 */
class Zend_Registry
{
    protected static $_storage = array();

    /**
     * @param string $index
     * @param mixed $value
     */
    public static function set($index, $value)
    {
        self::$_storage[$index] = $value;
    }

    /**
     * @param string $index
     * @return mixed
     */
    public static function get($index)
    {
        return isset(self::$_storage[$index]) ? self::$_storage[$index] : null;
    }
}
