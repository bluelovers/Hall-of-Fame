<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Reflection_Class extends ReflectionClass
{

	public function getProperties($filter = null)
	{
		if (!$filter)
		{
			return parent::getProperties();
		}

		$isStatic = $isProp = $isPublic = $isProtected = $isPrivate = false;

		($filter & Sco_Reflection_Property::IS_STATIC) && $isStatic = true;
		($filter ^ Sco_Reflection_Property::IS_STATIC || $filter & Sco_Reflection_Property::IS_PROP) && $isProp = true;
		($filter & Sco_Reflection_Property::IS_PUBLIC) && $isPublic = true;
		($filter & Sco_Reflection_Property::IS_PROTECTED) && $isProtected = true;
		($filter & Sco_Reflection_Property::IS_PRIVATE) && $isPrivate = true;

		//debug($filter, $isStatic, $isProp, $isPublic, $isProtected, $isPrivate);

		$props = array();

		foreach ($this->getProperties() as $prop)
		{
			if (($isProp && !$prop->isStatic() || $isStatic && $prop->isStatic()) && (!($isPublic || $isProtected || $isPrivate) || ($isPublic && $prop->isPublic() || $isProtected && $prop->isProtected() || $isPrivate && $prop->isPrivate())))
			{
				$props[] = $prop;
			}
		}

		return (array )$props;
	}

}
