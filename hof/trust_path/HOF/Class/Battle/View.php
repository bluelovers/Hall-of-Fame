<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_View extends HOF_Class_Array
{

	protected $battle = null;

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		parent::__construct($this->_data_default);

		$this->battle = &$battle;
	}

	function output()
	{
		return $this->__toString();
	}

	function __toString()
	{
		return is_array($this->output) ? implode('', (array)$this->output) : (string)$this->output;
	}

	/**
	 * 戦闘終了時に表示
	 */
	function BattleFoot()
	{
		$this->output[] = '</tbody></table>';
	}

}

