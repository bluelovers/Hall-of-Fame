<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Filter_IdentityCard_ROC implements Zend_Filter_Interface
{

	public function filter($value)
    {
    	if (preg_match(Sco_IdentityCard_ROC::REGEX_FILTER, $value, $m))
    	{
    		return strtoupper($m[0]);
    	}

        return null;
    }

}