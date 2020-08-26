<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * @link http://ar2rsawseen.users.phpclasses.org/package/6399-PHP-Generate-QR-Code-images-using-Google-Chart-API.html
 * @link http://webcodingeasy.com/PHP-classes/QR-code-generator-class
 * @link http://zxing.appspot.com/generator
 * @link http://zxing.org/w/decode.jspx
 */
class Sco_Chart_QRCode
{

	const FORMAT_BOOKMARK = 'MEBKM:TITLE:%s;URL:%s;;';
	const FORMAT_MECARD = 'MECARD:N:%s;ADR:%s;TEL:%s;EMAIL:%s;;';
	const FORMAT_GEO = 'GEO:%s,%s?q=%s';
	const FORMAT_MAILTO = 'MATMSG:TO:%s;SUB:%s;BODY:%s;;';
	const FORMAT_SMSTO = 'SMSTO:%s:%s';
	const FORMAT_TEL = 'TEL:%s';
	const FORMAT_TEXT = '%s';
	const FORMAT_URL = '%s';
	const FORMAT_WIFI = 'WIFI:S:%s;T:%s;P:%s;;';

	/**
	 * 7%
	 */
	const EC_L = 'L';
	/**
	 * 15%
	 */
	const EC_M = 'M';
	/**
	 * 25%
	 */
	const EC_Q = 'Q';
	/**
	 * 30%
	 */
	const EC_H = 'H';

	const SIZE_DEF = 120;

	const CHARSET = 'UTF-8';
	const IM_TYPE = 'png';

	/**
	 * @var Sco_Chart_QRCode_Adapter_Abstract
	 */
	protected static $_adapter_class = 'Sco_Chart_QRCode_Adapter_Google';
	protected static $_adapter_size = self::SIZE_DEF;
	protected static $_adapter_ec = self::EC_H;
	protected static $_adapter_options = array(
		'size' => self::SIZE_DEF,
		'ec' => self::EC_H,
		'charset' => self::CHARSET,
		'type' => self::IM_TYPE,
		);

	/**
	 * @var Sco_Chart_QRCode_Adapter_Abstract
	 */
	protected $_adapter;

	/**
	 * @var string
	 */
	protected $_content = null;

	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @param Sco_Chart_QRCode_Adapter_Abstract $adapter_class
	 *
	 * @return self
	 */
	public function __construct($content = null, $options = array(), $adapter_class = null)
	{
		$this->setOptions(array_merge(self::$_adapter_options, (array )$options));
		$this->setAdapter($adapter_class);
		$this->setContent($content);

		return $this;
	}

	public static function newInstance($content = null, $options = array(), $adapter_class = null)
	{
		return new self($content, $options, $adapter_class);
	}

	/**
	 * @param Sco_Chart_QRCode_Adapter_Abstract $adapter_class
	 *
	 * @return string
	 */
	public static function setAdapterDefaultClass($adapter_class)
	{
		$old = self::$_adapter_class;
		self::$_adapter_class = (string )$adapter_class;
		return $old;
	}

	public static function getAdapterDefaultClass()
	{
		return self::$_adapter_class;
	}

	public static function setAdapterDefaultSize($adapter_size)
	{
		$old = self::$_adapter_size;
		self::$_adapter_size = (int)$adapter_size;
		return $old;
	}

	public static function getAdapterDefaultSize()
	{
		return self::$_adapter_size;
	}

	public static function setAdapterDefaultEc($adapter_ec)
	{
		$old = self::$_adapter_ec;
		self::$_adapter_ec = (int)$adapter_ec;
		return $old;
	}

	public static function getAdapterDefaultEc()
	{
		return self::$_adapter_ec;
	}

	public static function setAdapterDefaultOptions($adapter_options)
	{
		$old = self::$_adapter_options;
		self::$_adapter_options = $adapter_options;
		return $old;
	}

	public static function getAdapterDefaultOptions()
	{
		return (array )self::$_adapter_options;
	}

	/**
	 * @param Sco_Chart_QRCode_Adapter_Abstract $adapter_class
	 *
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function setAdapter($adapter_class)
	{
		if ($adapter_class === null)
		{
			$adapter_class = self::$_adapter_class;
		}

		$old = $this->_adapter;

		$null = null;
		$adapter = &$null;

		if (!is_object($adapter_class))
		{
			$adapter = new $adapter_class;
		}
		else
		{
			$adapter = $adapter_class;
		}

		if (empty($adapter) || !$adapter instanceof Sco_Chart_QRCode_Adapter_Abstract)
		{
			throw new InvalidArgumentException('\'%s\' must instanceof %s', get_class($adapter), 'Sco_Chart_QRCode_Adapter_Abstract');
		}

		$this->_adapter = $adapter;

		$this->_adapter->bindTo($this);

		return $old;
	}

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract $adapter_class
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}

	/**
	 * setContent()
	 *
	 * @param string $content
	 * @return self
	 */
	public function setContent($content)
	{
		$this->_adapter->setContent($content);

		return $this;
	}

	/**
	 * getContent()
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->_adapter->getContent();
	}

	public function &setSize($size)
	{
		if ($size === null)
		{
			$size = Sco_Chart_QRCode::getAdapterDefaultSize();
		}

		$this->_options['size'] = (int)$size;
		return $this;
	}

	public function getSize($size)
	{
		if ($this->_options['size'] === null)
		{
			$this->setSize(null);
		}

		return $this->_options['size'];
	}

	public function &setEc($ec)
	{
		if ($ec === null)
		{
			$ec = Sco_Chart_QRCode::getAdapterDefaultEc();
		}

		$this->_options['ec'] = $ec;
		return $this;
	}

	public function getEc($size)
	{
		if ($this->_options['ec'] === null)
		{
			$this->setEc(null);
		}

		return $this->_options['ec'];
	}

	public function &setOptions($options, $sync = false)
	{
		foreach ($options as $k => $v)
		{
			$this->_options[$k] = $v;
		}

		if ($sync)
		{
			$this->_adapter->setOptions($this->_options);
		}

		return $this;
	}

	public function getOptions()
	{
		return (array )$this->_options;
	}

	public function &setCharset($charset)
	{
		if ($charset === null)
		{
			$charset = Sco_Chart_QRCode::CHARSET;
		}

		$this->_options['charset'] = $charset;
		return $this;
	}

	public function getCharset()
	{
		if ($this->_options['charset'] === null)
		{
			$this->setCharset(null);
		}

		return $this->_options['charset'];
	}

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function make()
	{
		return $this->_adapter->setOptions($this->_options)->make();
	}

	/**
	 * creating code with link mtadata
	 *
	 * @return self
	 */
	public function do_url($url)
	{
		if (preg_match('/^https?:\/\//', $url))
		{
			$this->setContent(sprintf(self::FORMAT_URL, $url));
		}
		else
		{
			$this->setContent(sprintf(self::FORMAT_URL, "http://" . $url));
		}

		return $this;
	}

	/**
	 * creating code with bookmark metadata
	 *
	 * @return self
	 */
	public function do_bookmark($title, $url)
	{
		$this->setContent(sprintf(self::FORMAT_BOOKMARK, $title, $url));

		return $this;
	}

	/**
	 * creating text qr code
	 *
	 * @return self
	 */
	public function do_text($text)
	{
		$this->setContent($text);

		return $this;
	}

	/**
	 * creatng code with sms metadata
	 *
	 * @return self
	 */
	public function do_smsto($phone, $text)
	{
		$this->setContent(sprintf(self::FORMAT_SMSTO, $phone, $text));

		return $this;
	}

	/**
	 * creating code with phone
	 *
	 * @return self
	 */
	public function do_tel($phone)
	{
		$this->setContent(sprintf(self::FORMAT_TEL, $phone));

		return $this;
	}

	/**
	 * creating code with mecard metadata
	 *
	 * @return self
	 */
	public function do_mecard($name, $address, $phone, $email)
	{
		$this->setContent(sprintf(self::FORMAT_MECARD, $name, $address, $phone, $email));

		return $this;
	}

	/**
	 * creating code wth email metadata
	 *
	 * @return self
	 */
	public function do_mailto($email, $subject, $message)
	{
		$this->setContent(sprintf(self::FORMAT_MAILTO, $email, $subject, $message));

		return $this;
	}

	/**
	 * creating code with geo location metadata
	 *
	 * @return self
	 */
	public function do_geo($latitude, $longitude, $query = '')
	{
		$this->setContent(sprintf(self::FORMAT_GEO, $latitude, $longitude, $query));

		return $this;
	}

	/**
	 * creating code with wifi configuration metadata
	 *
	 * @return self
	 */
	public function do_wifi($ssid, $type = 'nopass', $pass = '')
	{
		$this->setContent(sprintf(self::FORMAT_WIFI, $type, $ssid, $pass));

		return $this;
	}

	/**
	 * creating code with i-appli activating meta data
	 *
	 * @return self
	 */
	public function do_iappli($adf, $cmd, $param)
	{
		$param_str = '';
		foreach ($param as $val)
		{
			$param_str .= "PARAM:" . $val["name"] . "," . $val["value"] . ";";
		}
		$this->setContent("LAPL:ADFURL:" . $adf . ";CMD:" . $cmd . ";" . $param_str . ";");

		return $this;
	}

	public function createURI()
	{
		return call_user_func_array(array($this->_adapter, __FUNCTION__), func_get_args());
	}

	public function createImage()
	{
		return call_user_func_array(array($this->_adapter, __FUNCTION__), func_get_args());
	}

	public function createFile()
	{
		return call_user_func_array(array($this->_adapter, __FUNCTION__), func_get_args());
	}

	public function createHtml()
	{
		return call_user_func_array(array($this->_adapter, __FUNCTION__), func_get_args());
	}

}
