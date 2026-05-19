<?php

/**
 * POSIX 風格路徑處理輔助類別
 * POSIX style path handling helper class
 *
 * 繼承 HOF_Helper_Path，使用 '/' 作為路徑分隔符
 * Extends HOF_Helper_Path, uses '/' as path separator
 *
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Helper_PathPosix extends HOF_Helper_Path
{
	/**
	 * POSIX 風格路徑分隔符號
	 * POSIX style path separator
	 *
	 * @var string
	 */
	public static $sep = '/';
}