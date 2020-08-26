<?php

/**
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Class_Base extends HOF_Class_Array
{
    public $file;
    public $fp;

    public $data = [];

    public function __construct()
    {
        $data = get_object_vars($this);

        parent::__construct((array) $data);

        if (!$this->_fpinit()) {
            return false;
        }
    }

    public function __destruct()
    {
        $this->fpclose();
    }

    public function _fpname()
    {
        return $this->file;
    }

    public function fpopen($over = null, $autocreate = false)
    {
        if (!$this->fp || $over) {
            $args = func_get_args();
            $ret = call_user_func_array([$this, '_' . __FUNCTION__], $args);

            $this->fp = HOF_Class_File::fplock_file($this->_fpname(), false, $autocreate);
        }

        return null !== $ret ? $ret : $this->fp;
    }

    public function _fpopen($over, $file)
    {
    }

    public function fpread()
    {
        $args = func_get_args();

        $ret = call_user_func_array([$this, '_' . __FUNCTION__], $args);

        return null !== $ret ? $ret : $this;
    }

    public function _fpread()
    {
    }

    public function fpsave($not_close = null)
    {
        $args = func_get_args();
        $ret = call_user_func_array([$this, '_' . __FUNCTION__], $args);

        return ($not_close || null !== $ret) ? $ret : $this->fpclose();
    }

    public function _fpsave()
    {
    }

    public function fpclose()
    {
        $ret = true;

        if ($this->fp) {
            $args = func_get_args();
            $ret = call_user_func_array([$this, '_' . __FUNCTION__], $args);

            if (!@fclose($this->fp)) {
                $ret = false;
            }

            unset($this->fp);
        }

        return $ret;
    }

    public function _fpclose()
    {
    }

    public function dump()
    {
        return print_r($this->toArray(), 1);
    }
}
