<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Text_Helper
{

	public static $map_h2f;

	/**
	 * @see http://www.php.net/manual/en/function.preg-replace.php#87816
	 */
	public static function lf($str, $eol = NL, $search = CR)
	{
		/*
		http://www.php.net/manual/en/function.preg-replace.php#87816

		$sql = preg_replace("/(?<!\\n)\\r+(?!\\n)/", "\r\n", $sql);
		$sql = preg_replace("/(?<!\\r)\\n+(?!\\r)/", "\r\n", $sql);
		$sql = preg_replace("/(?<!\\r)\\n\\r+(?!\\n)/", "\r\n", $sql);
		*/

		($search === null || $search === false) && $search = CR;

		if (strpos($str, $search) !== false)
		{
			$str = preg_replace("/(?<!\\n)\\r+(?!\\n)/", CRLF, $str);
			$str = preg_replace("/(?<!\\r)\\n+(?!\\r)/", CRLF, $str);
			$str = preg_replace("/(?<!\\r)\\n\\r+(?!\\n)/", CRLF, $str);

			($eol === null || $eol === false) && $eol = NL;

			($eol != CRLF) && $str = str_replace(CRLF, $eol, $str);
		}

		return $str;
	}

	public static function str2hex($string)
	{
		$hex = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}

	public static function hex2str($hex)
	{
		$string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2)
		{
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
	}

	function str_f2h($str, $case = 0)
	{
		if (!isset($map_h2f))
		{
			$map_h2f = array(
				' ' => '　',
				'0' => '０',
				'1' => '１',
				'2' => '２',
				'3' => '３',
				'4' => '４',
				'5' => '５',
				'6' => '６',
				'7' => '７',
				'8' => '８',
				'9' => '９',
				'A' => 'Ａ',
				'B' => 'Ｂ',
				'C' => 'Ｃ',
				'D' => 'Ｄ',
				'E' => 'Ｅ',
				'F' => 'Ｆ',
				'G' => 'Ｇ',
				'H' => 'Ｈ',
				'I' => 'Ｉ',
				'J' => 'Ｊ',
				'K' => 'Ｋ',
				'L' => 'Ｌ',
				'M' => 'Ｍ',
				'N' => 'Ｎ',
				'O' => 'Ｏ',
				'P' => 'Ｐ',
				'Q' => 'Ｑ',
				'R' => 'Ｒ',
				'S' => 'Ｓ',
				'T' => 'Ｔ',
				'U' => 'Ｕ',
				'V' => 'Ｖ',
				'W' => 'Ｗ',
				'X' => 'Ｘ',
				'Y' => 'Ｙ',
				'Z' => 'Ｚ',
				'a' => 'ａ',
				'b' => 'ｂ',
				'c' => 'ｃ',
				'd' => 'ｄ',
				'e' => 'ｅ',
				'f' => 'ｆ',
				'g' => 'ｇ',
				'h' => 'ｈ',
				'i' => 'ｉ',
				'j' => 'ｊ',
				'k' => 'ｋ',
				'l' => 'ｌ',
				'm' => 'ｍ',
				'n' => 'ｎ',
				'o' => 'ｏ',
				'p' => 'ｐ',
				'q' => 'ｑ',
				'r' => 'ｒ',
				's' => 'ｓ',
				't' => 'ｔ',
				'u' => 'ｕ',
				'v' => 'ｖ',
				'w' => 'ｗ',
				'x' => 'ｘ',
				'y' => 'ｙ',
				'z' => 'ｚ',
				'~' => '～',
				'!' => '！',
				'@' => '＠',
				'#' => '＃',
				'$' => '＄',
				'%' => '％',
				'^' => '︿',
				'&' => '＆',
				'*' => '＊',
				'(' => '（',
				')' => '）',
				'_' => '＿',
				'+' => '＋',
				'|' => '｜',
				'`' => '‘',
				'-' => '－',
				'=' => '＝',
				'\\' => '＼',
				'{' => '｛',
				'}' => '｝',
				'[' => '〔',
				']' => '〕',
				':' => '：',
				'"' => '”',
				';' => '；',
				'\'' => '’',
				'<' => '＜',
				'>' => '＞',
				'?' => '？',
				',' => '，',
				'.' => '．',
				'/' => '／',
				);
		}

		return $case ? strtr((string )$str, $map_h2f) : strtr((string )$str, array_flip($map_h2f));
	}

	public static function chunk_split_unicode($str, $l = 76, $e = NL)
	{
		$tmp = array_chunk(preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY), $l);
		$str = '';
		foreach ($tmp as $t)
		{
			$str .= join('', $t) . $e;
		}
		return $str;
	}

	/**
	 * Tests whether a string contains only 7bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to check
	 * @return  bool
	 */
	public static function is_ascii($str)
	{
		return is_string($str) and !preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string   string to transliterate
	 * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		static $UTF8_LOWER_ACCENTS = null;
		static $UTF8_UPPER_ACCENTS = null;

		if ($case <= 0)
		{
			if ($UTF8_LOWER_ACCENTS === null)
			{
				$UTF8_LOWER_ACCENTS = array(
					'à' => 'a',
					'ô' => 'o',
					'ď' => 'd',
					'ḟ' => 'f',
					'ë' => 'e',
					'š' => 's',
					'ơ' => 'o',
					'ß' => 'ss',
					'ă' => 'a',
					'ř' => 'r',
					'ț' => 't',
					'ň' => 'n',
					'ā' => 'a',
					'ķ' => 'k',
					'ŝ' => 's',
					'ỳ' => 'y',
					'ņ' => 'n',
					'ĺ' => 'l',
					'ħ' => 'h',
					'ṗ' => 'p',
					'ó' => 'o',
					'ú' => 'u',
					'ě' => 'e',
					'é' => 'e',
					'ç' => 'c',
					'ẁ' => 'w',
					'ċ' => 'c',
					'õ' => 'o',
					'ṡ' => 's',
					'ø' => 'o',
					'ģ' => 'g',
					'ŧ' => 't',
					'ș' => 's',
					'ė' => 'e',
					'ĉ' => 'c',
					'ś' => 's',
					'î' => 'i',
					'ű' => 'u',
					'ć' => 'c',
					'ę' => 'e',
					'ŵ' => 'w',
					'ṫ' => 't',
					'ū' => 'u',
					'č' => 'c',
					'ö' => 'o',
					'è' => 'e',
					'ŷ' => 'y',
					'ą' => 'a',
					'ł' => 'l',
					'ų' => 'u',
					'ů' => 'u',
					'ş' => 's',
					'ğ' => 'g',
					'ļ' => 'l',
					'ƒ' => 'f',
					'ž' => 'z',
					'ẃ' => 'w',
					'ḃ' => 'b',
					'å' => 'a',
					'ì' => 'i',
					'ï' => 'i',
					'ḋ' => 'd',
					'ť' => 't',
					'ŗ' => 'r',
					'ä' => 'a',
					'í' => 'i',
					'ŕ' => 'r',
					'ê' => 'e',
					'ü' => 'u',
					'ò' => 'o',
					'ē' => 'e',
					'ñ' => 'n',
					'ń' => 'n',
					'ĥ' => 'h',
					'ĝ' => 'g',
					'đ' => 'd',
					'ĵ' => 'j',
					'ÿ' => 'y',
					'ũ' => 'u',
					'ŭ' => 'u',
					'ư' => 'u',
					'ţ' => 't',
					'ý' => 'y',
					'ő' => 'o',
					'â' => 'a',
					'ľ' => 'l',
					'ẅ' => 'w',
					'ż' => 'z',
					'ī' => 'i',
					'ã' => 'a',
					'ġ' => 'g',
					'ṁ' => 'm',
					'ō' => 'o',
					'ĩ' => 'i',
					'ù' => 'u',
					'į' => 'i',
					'ź' => 'z',
					'á' => 'a',
					'û' => 'u',
					'þ' => 'th',
					'ð' => 'dh',
					'æ' => 'ae',
					'µ' => 'u',
					'ĕ' => 'e',
					'ı' => 'i',
					);
			}

			//$str = str_replace(array_keys($UTF8_LOWER_ACCENTS), array_values($UTF8_LOWER_ACCENTS), $str);
			$str = strtr((string )$str, $UTF8_LOWER_ACCENTS);
		}

		if ($case >= 0)
		{
			if ($UTF8_UPPER_ACCENTS === null)
			{
				$UTF8_UPPER_ACCENTS = array(
					'À' => 'A',
					'Ô' => 'O',
					'Ď' => 'D',
					'Ḟ' => 'F',
					'Ë' => 'E',
					'Š' => 'S',
					'Ơ' => 'O',
					'Ă' => 'A',
					'Ř' => 'R',
					'Ț' => 'T',
					'Ň' => 'N',
					'Ā' => 'A',
					'Ķ' => 'K',
					'Ĕ' => 'E',
					'Ŝ' => 'S',
					'Ỳ' => 'Y',
					'Ņ' => 'N',
					'Ĺ' => 'L',
					'Ħ' => 'H',
					'Ṗ' => 'P',
					'Ó' => 'O',
					'Ú' => 'U',
					'Ě' => 'E',
					'É' => 'E',
					'Ç' => 'C',
					'Ẁ' => 'W',
					'Ċ' => 'C',
					'Õ' => 'O',
					'Ṡ' => 'S',
					'Ø' => 'O',
					'Ģ' => 'G',
					'Ŧ' => 'T',
					'Ș' => 'S',
					'Ė' => 'E',
					'Ĉ' => 'C',
					'Ś' => 'S',
					'Î' => 'I',
					'Ű' => 'U',
					'Ć' => 'C',
					'Ę' => 'E',
					'Ŵ' => 'W',
					'Ṫ' => 'T',
					'Ū' => 'U',
					'Č' => 'C',
					'Ö' => 'O',
					'È' => 'E',
					'Ŷ' => 'Y',
					'Ą' => 'A',
					'Ł' => 'L',
					'Ų' => 'U',
					'Ů' => 'U',
					'Ş' => 'S',
					'Ğ' => 'G',
					'Ļ' => 'L',
					'Ƒ' => 'F',
					'Ž' => 'Z',
					'Ẃ' => 'W',
					'Ḃ' => 'B',
					'Å' => 'A',
					'Ì' => 'I',
					'Ï' => 'I',
					'Ḋ' => 'D',
					'Ť' => 'T',
					'Ŗ' => 'R',
					'Ä' => 'A',
					'Í' => 'I',
					'Ŕ' => 'R',
					'Ê' => 'E',
					'Ü' => 'U',
					'Ò' => 'O',
					'Ē' => 'E',
					'Ñ' => 'N',
					'Ń' => 'N',
					'Ĥ' => 'H',
					'Ĝ' => 'G',
					'Đ' => 'D',
					'Ĵ' => 'J',
					'Ÿ' => 'Y',
					'Ũ' => 'U',
					'Ŭ' => 'U',
					'Ư' => 'U',
					'Ţ' => 'T',
					'Ý' => 'Y',
					'Ő' => 'O',
					'Â' => 'A',
					'Ľ' => 'L',
					'Ẅ' => 'W',
					'Ż' => 'Z',
					'Ī' => 'I',
					'Ã' => 'A',
					'Ġ' => 'G',
					'Ṁ' => 'M',
					'Ō' => 'O',
					'Ĩ' => 'I',
					'Ù' => 'U',
					'Į' => 'I',
					'Ź' => 'Z',
					'Á' => 'A',
					'Û' => 'U',
					'Þ' => 'Th',
					'Ð' => 'Dh',
					'Æ' => 'Ae',
					'İ' => 'I',
					);
			}

			//$str = str_replace(array_keys($UTF8_UPPER_ACCENTS), array_values($UTF8_UPPER_ACCENTS), $str);
			$str = strtr((string )$str, $UTF8_UPPER_ACCENTS);
		}

		return $str;
	}

	/**
	 * @see http://tw2.php.net/manual/en/function.urldecode.php#62707
	 */
	function code2utf($num)
	{
		if ($num < 128) return chr($num);
		if ($num < 1024) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
		if ($num < 32768) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);

		return '';
	}

	/**
	 * @see http://tw2.php.net/manual/en/function.urldecode.php#62707
	 */
	function unescape($strIn, $iconv_to = 'UTF-8')
	{
		$strOut = '';
		$iPos = 0;
		$len = strlen($strIn);
		while ($iPos < $len)
		{
			$charAt = substr($strIn, $iPos, 1);
			if ($charAt == '%')
			{
				$iPos++;
				$charAt = substr($strIn, $iPos, 1);
				if ($charAt == 'u')
				{
					// Unicode character
					$iPos++;
					$unicodeHexVal = substr($strIn, $iPos, 4);
					$unicode = hexdec($unicodeHexVal);
					$strOut .= self::code2utf($unicode);
					$iPos += 4;
				}
				else
				{
					// Escaped ascii character
					$hexVal = substr($strIn, $iPos, 2);
					if (hexdec($hexVal) > 127)
					{
						// Convert to Unicode
						$strOut .= self::code2utf(hexdec($hexVal));
					}
					else
					{
						$strOut .= chr(hexdec($hexVal));
					}
					$iPos += 2;
				}
			}
			else
			{
				$strOut .= $charAt;
				$iPos++;
			}
		}
		if ($iconv_to != "UTF-8")
		{
			$strOut = iconv("UTF-8", $iconv_to, $strOut);
		}
		return $strOut;
	}

	/**
	 * @see http://tw2.php.net/manual/en/function.urldecode.php#29272
	 * For compatibility of new and old brousers:
	 *		%xx -> char
	 *		%u0xxxx -> char
	 */
	function unicode_decode($txt)
	{
		$txt = ereg_replace('%u0([[:alnum:]]{3})', '&#x\1;', $txt);
		$txt = ereg_replace('%([[:alnum:]]{2})', '&#x\1;', $txt);
		return ($txt);
	}

}
