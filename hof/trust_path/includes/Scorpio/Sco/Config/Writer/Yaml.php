<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Config_Writer_Yaml extends Zend_Config_Writer_Yaml
{

	function _array_diff($array1, $array2)
	{
		$array3 = null;

		foreach ($array1 as $_k => $_v1)
    	{
    		if (($_v2 = $array2[$_k]) != $_v1)
    		{

    			if (is_array($_v1) && is_array($_v2))
	    		{
	    			if (null === ($_v1 = $this->_array_diff($_v1, $_v2)))
	    			{
	    				continue;
	    			}
	    		}

    			$array3[$_k] = $_v1;
    		}
    	}

    	return $array3;
	}

	public function render()
    {
    	if (method_exists($this->_config, 'toYaml'))
    	{
    		return $this->_config->toYaml();
    	}

        $data        = $this->_config->toArray();
        $sectionName = $this->_config->getSectionName();
        $extends     = $this->_config->getExtends();

        if (is_string($sectionName)) {
            $data = array($sectionName => $data);
        }

        foreach ($extends as $section => $parentSection) {
        	$v = $this->_array_diff($data[$section], $data[$parentSection]);

        	$data[$section] = null;
			$data[$section][Zend_Config_Yaml::EXTENDS_NAME] = $parentSection;

			if ($v !== null)
			{
				$data[$section] = $data[$section] + $v;
			}
        }

        // Ensure that each "extends" section actually exists
        foreach ($data as $section => $sectionData) {
            if (is_array($sectionData) && isset($sectionData[Zend_Config_Yaml::EXTENDS_NAME])) {
                $sectionExtends = $sectionData[Zend_Config_Yaml::EXTENDS_NAME];
                if (!isset($data[$sectionExtends])) {
                    // Remove "extends" declaration if section does not exist
                    unset($data[$section][Zend_Config_Yaml::EXTENDS_NAME]);
                }
            }
        }

        return call_user_func($this->getYamlEncoder(), $data);
    }

}
