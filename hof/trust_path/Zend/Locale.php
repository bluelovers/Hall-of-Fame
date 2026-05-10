<?php

/**
 * Minimal Zend_Locale stub
 */
class Zend_Locale
{
    protected $_locale;

    /**
     * @param string $locale
     */
    public function __construct($locale = null)
    {
        $this->_locale = $locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_locale;
    }
}
