<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Reflection_Function extends Zend_Reflection_Function
{

	/**
	 * Get function body
	 *
	 * @return string
	 */
	public function getBody()
	{
		$filename = $this->getFileName();

		if ($this->isInternal() || empty($filename))
		{
			throw new Zend_Reflection_Exception($this->getName() . ' cannot get body or is internal function');
		}

		$lines = array_slice(file($filename, FILE_IGNORE_NEW_LINES), $this->getStartLine() - 1, max($this->getEndLine() - $this->getStartLine() + 1, 1), true);

		//var_dump($lines);

		do
		{
			$firstLine = array_shift($lines);
		} while (false === ($pos = strpos($firstLine, '{')) && $lines);

		//var_dump($lines, $firstLine, $pos);

		if ($line = substr($firstLine, $pos + 1))
		{
			//var_dump($line);

			array_unshift($lines, $line);
		}

		$lastLine = array_pop($lines);

		if (trim($lastLine) !== '}')
		{
			array_push($lines, $lastLine);
		}

		// just in case we had code on the bracket lines
		return rtrim(ltrim(implode("\n", $lines), '{'), '}');
	}

}
