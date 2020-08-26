<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Reflection_Property extends ReflectionProperty
{

	const IS_PROP = 4096;
	const IS_ALL = 1793;

	public function isProperty()
	{
		return (bool)!$this->isStatic();
	}

}
