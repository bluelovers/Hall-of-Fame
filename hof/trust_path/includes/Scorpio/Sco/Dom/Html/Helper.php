<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Dom_Html_Helper
{

	# Boolean attributes, which may have the value omitted entirely.  Manually
	# collected from the HTML5 spec as of 2010-06-07.
	protected static $boolAttribs = array(
		'async',
		'autofocus',
		'autoplay',
		'checked',
		'controls',
		'defer',
		'disabled',
		'formnovalidate',
		'hidden',
		'ismap',
		'itemscope',
		'loop',
		'multiple',
		'novalidate',
		'open',
		'pubdate',
		'readonly',
		'required',
		'reversed',
		'scoped',
		'seamless',
		'selected',
	);

	/**
	 * mediawiki
	 **/
	public static function makeattr($attr = array(), $xml = false) {
		$ret = '';

		if (empty($attr)) return $ret;

		$attr = array_unique((array)$attr);
		foreach ($attr as $_k => $_v) {
			if ($_v === null) {
				continue;
			}

			# For boolean attributes, support array( 'foo' ) instead of
			# requiring array( 'foo' => 'meaningless' ).
			if ( is_int( $_k ) && in_array( strtolower( $_v ), static::$boolAttribs ) ) {
				$_k = $_v;
			} elseif ($_k == 'lang') {
				$attr['xml:lang'] = $_v;
			}

			$_k = strtolower( $_k );

			if ( in_array( $_k, static::$boolAttribs ) ) {
				$ret .= ' '.$_k.'="'.$_k.'"';
			} elseif (is_numeric( $_k )) {
				continue;
			} else {
				# Apparently we need to entity-encode \n, \r, \t, although the
				# spec doesn't mention that.  Since we're doing strtr() anyway,
				# and we don't need <> escaped here, we may as well not call
				# htmlspecialchars().  FIXME: verify that we actually need to
				# escape \n\r\t here, and explain why, exactly.
				#
				# We could call Sanitizer::encodeAttribute() for this, but we
				# don't because we're stubborn and like our marginal savings on
				# byte size from not having to encode unnecessary quotes.
				$map = array(
					'&' => '&amp;',
					'"' => '&quot;',
					"\n" => '&#10;',
					"\r" => '&#13;',
					"\t" => '&#9;'
				);

				if ( $xml ) {
					# This is allowed per spec: <http://www.w3.org/TR/xml/#NT-AttValue>
					# But reportedly it breaks some XML tools?  FIXME: is this
					# really true?
					$map['<'] = '&lt;';
				}

				$ret .= ' '.$_k.'="' . strtr( static::val2html($_v), $map ) . '"';
			}
		}
		return $ret;
	}

	public static function val2html($value) {
		if ($value === true) {
			return 'true';
		} elseif ($value === false) {
			return 'false';
		} else {
			return $value;
		}
	}

	public static function elem($tag, $contents = '', $attr = array(), $noclose = false, $noend = false) {
		$retattr = static::makeattr($attr);

		$ret = '<'.$tag.' '.$retattr.($noend ? '' : '>'.$contents.($noclose ? '' : '</'.$tag.'>'));

		return $ret;
	}

	public static function strip($str, $allowable_tags = '') {
		return strip_tags((string)$str, $allowable_tags);
	}

}
