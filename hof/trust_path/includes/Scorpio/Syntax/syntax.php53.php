<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

if (!function_exists('class_alias') && version_compare(PHP_VERSION, '5.3', '<'))
{

	/**
	 * class_alias for php < 5.3 only
	 */
	function class_alias($source, $alias)
	{
		$ref = new ReflectionClass($source);

		$regex = '/(^[a-zA-Z_][a-zA-Z0-9_]*$)/';

		if ($ref->isInterface() || $ref->isFinal())
		{
			$type = '';

			if ($ref->isFinal())
			{
				$type .= ' Final';
			}

			if ($ref->isInterface())
			{
				$type .= ' Interface';
			}

			throw new Exception(sprintf('Fatal error: Class %s cannot alias from%s %s IN PHP%s', $source, $type, $alias, PHP_VERSION));
		}
		/*
		elseif (!preg_match($regex, $alias))
		{
			throw new Exception(sprintf('Fatal error: Bad Class Name %s', $alias));
		}
		*/
		elseif (empty($source) || empty($alias) || !preg_match($regex, $source) || !preg_match($regex, $alias))
		{
			throw new Exception(sprintf('Fatal error: Bad Class Name %s, %s', $source, $alias));
		}
		elseif (!class_exists($source) || !$ref)
		{
			throw new Exception(sprintf('Fatal error: Class %s does not exist', $source));
		}
		elseif (class_exists($alias, false) || interface_exists($alias, false))
		{
			throw new Exception(sprintf('Fatal error: Class %s already exists', $alias));
		}

		$eval = 'class %s extends %s {}';

		if ($ref->isAbstract())
		{
			$eval = 'abstract ' . $eval;
		}

		eval(sprintf($eval, $alias, $source));

		return class_exists($alias, false);
	}

}
