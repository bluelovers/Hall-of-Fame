<?php

/**
 * Minimal Zend_Date stub
 * Only implements features used by HOF
 */
class Zend_Date
{
    const DATETIME_FULL = 'yyyy-MM-dd HH:mm:ss';
    const TIME_FULL = 'HH:mm:ss';
    const DAY = 'dd';
    const TIMESTAMP = 'U';

    protected $_timestamp;
    protected $_locale;
    protected $_format;
    protected $_timezone;

    protected static $_options = array();

    /**
     * @param mixed $date
     * @param string $format
     * @param Zend_Locale|string $locale
     */
    public function __construct($date = null, $format = null, $locale = null)
    {
        $this->_locale = $locale;
        $this->_format = $format;
        $this->_timezone = date_default_timezone_get();

        if ($date === null)
        {
            $this->_timestamp = time();
        }
        elseif ($date instanceof Zend_Date)
        {
            $this->_timestamp = $date->_timestamp;
            $this->_timezone = $date->_timezone;
        }
        elseif (is_numeric($date))
        {
            $this->_timestamp = (int) $date;
        }
        elseif (is_string($date))
        {
            $this->_timestamp = strtotime($date);
        }
        elseif (is_array($date))
        {
            $this->_timestamp = time();
        }
        else
        {
            $this->_timestamp = time();
        }
    }

    /**
     * @param array $options
     * @return array
     */
    public static function setOptions(array $options = null)
    {
        if (null !== $options)
        {
            self::$_options = array_merge(self::$_options, $options);
        }
        return self::$_options;
    }

    /**
     * @param string $format
     * @param string $locale
     * @return string
     */
    public function toString($format = null, $locale = null)
    {
        if ($format === null)
        {
            $format = self::DATETIME_FULL;
        }

        return date($format, $this->_timestamp);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString(self::DATETIME_FULL);
    }

    /**
     * @return Zend_Locale|string|null
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    public function setTimezone($zone)
    {
        $this->_timezone = $zone;
        return $this;
    }

    public function setTimestamp($timestamp)
    {
        $this->_timestamp = (int) $timestamp;
        return $this;
    }

    public function getHour($locale = null)
    {
        return (int) date('G', $this->_timestamp);
    }

    public function getValue()
    {
        return $this->_timestamp;
    }

    public function sub($date, $part = null, $locale = null)
    {
        $result = new self($this->_timestamp);
        if ($date instanceof Zend_Date)
        {
            $result->_timestamp = $this->_timestamp - $date->_timestamp;
        }
        else
        {
            $result->_timestamp = $this->_timestamp - (int) $date;
        }
        return $result;
    }
}
