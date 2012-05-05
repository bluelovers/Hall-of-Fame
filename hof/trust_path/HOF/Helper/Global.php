<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Global
{

	/**
	 * お金の表示方式
	 */
	function MoneyFormat($number, $pre = '$&nbsp;')
	{
		return $pre . number_format($number);
	}

}
