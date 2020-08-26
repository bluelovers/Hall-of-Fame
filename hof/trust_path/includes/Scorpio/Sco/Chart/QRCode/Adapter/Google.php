<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Chart_QRCode_Adapter_Google extends Sco_Chart_QRCode_Adapter_Abstract
{

	const URI = 'http://chart.apis.google.com/chart';
	const URI_ARGV = 'chs=%1$dx%1$d&cht=qr&chld=%2$s|%4$d&chl=%3$s&choe=%5$s';

	public function createURI()
	{
		if ($this->_make() && isset($this->uri))
		{
			return (string )$this->uri;
		}

		return $this->uri = sprintf(self::URI . '?' . self::URI_ARGV, $this->_options['size'], $this->_options['ec'], urlencode($this->_content), $this->_options['margin'], $this->_options['charset']);
	}

	/**
	 * getting image
	 */
	public function createImage($type = null)
	{

		if ($this->_make() && isset($this->im) && isset($this->type))
		{
			return array($this->im, $this->type);
		}

		$this->type = ($type) ? (string )$type : (string )$this->_options['type'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::URI);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, sprintf(self::URI_ARGV, $this->_options['size'], $this->_options['ec'], urlencode($this->_content), $this->_options['margin'], $this->_options['charset']));

		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);

		$this->im = curl_exec($ch);
		curl_close($ch);

		$this->im = imagecreatefromstring($this->im);

		$transparent_new = imagecolorallocatealpha($this->im, 255, 255, 255, 127);
		imagecolortransparent($this->im, $transparent_new);

		imagealphablending($this->im, true);
		imagesavealpha($this->im, true);

		$color_photo = imagecreatetruecolor($this->_options['size'], $this->_options['size']);

		$transparent_new = imagecolorallocatealpha($color_photo, 255, 255, 255, 127);
		//imagefill($color_photo, 0, 0, $transparent_new);
		imagecolortransparent($color_photo, $transparent_new);

		imagecopymerge($color_photo, $this->im, 0, 0, 0, 0, $this->_options['size'], $this->_options['size'], 100);

		$this->im = $color_photo;

		return array($this->im, $this->type);
	}

	public function createFile($file = null, $type = null)
	{
		if ($this->_make() && isset($this->file))
		{
			return (string )$this->file;
		}

		list($this->im, $this->type) = $this->createImage($type);
		$this->file = $this->_file($file);

		//file_put_contents($this->file, $this->im, LOCK_EX);
		imagepng($this->im, $this->file);

		return $this->file;
	}

	public function createHtml()
	{
		if ($this->_make() && isset($this->html))
		{
			return (string )$this->html;
		}

		return $this->html = sprintf('<img src="%s" border="0" />', $this->createURI());
	}

	function __destruct()
	{
		@imagedestroy($this->im);
	}

}
