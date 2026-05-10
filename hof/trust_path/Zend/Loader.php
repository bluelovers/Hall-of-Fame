<?php

/**
 * Minimal Zend_Loader stub
 * Original: Zend Framework 1.x (c) 2005-2012 Zend Technologies
 */
class Zend_Loader
{
    public static function loadClass($class, $dirs = null)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        if (!empty($dirs)) {
            if (is_string($dirs)) {
                $dirs = array($dirs);
            }
            foreach ($dirs as $dir) {
                $path = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $file;
                if (file_exists($path)) {
                    require $path;
                    return;
                }
            }
        }

        if (stream_resolve_include_path($file)) {
            require $file;
        }
    }

    public static function loadFile($filename, $dirs = null, $once = false)
    {
        self::_securityCheck($filename);

        if ($dirs !== null) {
            if (is_string($dirs)) {
                $dirs = array($dirs);
            }
            foreach ($dirs as $dir) {
                $path = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($path)) {
                    if ($once) {
                        require_once $path;
                    } else {
                        require $path;
                    }
                    return;
                }
            }
        }

        if ($once) {
            require_once $filename;
        } else {
            require $filename;
        }
    }

    protected static function _securityCheck($filename)
    {
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            throw new Exception('Security check: Illegal character in filename');
        }
    }
}
