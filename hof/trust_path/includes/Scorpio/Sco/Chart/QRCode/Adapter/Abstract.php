<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class Sco_Chart_QRCode_Adapter_Abstract
{

	/**
	 * @var string
	 */
	protected $_content = null;

	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @var bool
	 */
	protected $_maked;

	/**
	 * @var Sco_Chart_QRCode
	 */
	protected $_qr;

	public $uri;
	public $im;
	public $type;
	public $file;
	public $html;

	/**
	 * @param Sco_Chart_QRCode $qr_obj
	 * @return self
	 */
	public function bindTo($qr_obj)
	{
		if (!$qr_obj instanceof Sco_Chart_QRCode)
		{
			throw new InvalidArgumentException('\'%s\' must instanceof %s', get_class($qr_obj), 'Sco_Chart_QRCode');
		}

		$null = null;
		$this->_qr = &$null;

		$this->_qr = $qr_obj;

		$this->setOptions($this->_qr->getOptions())->make();

		return $this;
	}

	/**
	 * @return Sco_Chart_QRCode
	 */
	public function getObject()
	{
		return $this->_qr;
	}

	/**
	 * setContent()
	 *
	 * @param string $content
	 * @return self
	 */
	public function &setContent($content)
	{
		$this->_content = (string )$content;
		return $this;
	}

	/**
	 * getContent()
	 *
	 * @return string
	 */
	public function getContent()
	{
		return (string )$this->_content;
	}

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
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

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function &setEc($ec)
	{
		if ($ec === null)
		{
			$ec = Sco_Chart_QRCode::getAdapterDefaultEc();
		}

		$this->_options['ec'] = $ec;
		return $this;
	}

	public function getEc()
	{
		if ($this->_options['ec'] === null)
		{
			$this->setEc(null);
		}

		return $this->_options['ec'];
	}

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function &setOptions($options)
	{
		foreach ($options as $k => $v)
		{
			$this->_options[$k] = $v;
		}

		return $this;
	}

	public function getOptions()
	{
		return (array )$this->_options;
	}

	/**
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function &setCharset($charset)
	{
		if ($charset === null)
		{
			$charset = Sco_Chart_QRCode::CHARSET;
		}

		$this->_options['charset'] = (string )$charset;
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
	 * make()
	 *
	 * @return Sco_Chart_QRCode_Adapter_Abstract
	 */
	public function make()
	{
		$this->_maked = false;

		unset($this->uri);
		unset($this->im);
		unset($this->type);
		unset($this->file);
		unset($this->html);

		$this->setSize($this->_options['size'])->setEc($this->_options['ec'])->setCharset($this->_options['charset']);

		$this->_maked = true;

		return $this;
	}

	protected function _make()
	{
		if (!$this->_maked)
		{
			$this->make();

			return false;
		}

		return true;
	}

	protected function _file($file)
	{
		if ($file === null)
		{
			if ($this->_options['file'])
			{
				$file = $this->_options['file'];
			}
			else
			{
				$file = 'qrcode.' . $this->type;
			}
		}

		return (string )$file;
	}

	public function createURI()
	{
		throw new BadMethodCallException(sprintf('%s::%s() not defined', get_class($this), __FUNCTION__ ));
	}

	public function createImage($type = null)
	{
		throw new BadMethodCallException(sprintf('%s::%s() not defined', get_class($this), __FUNCTION__ ));
	}

	public function createFile($file = null, $type = null)
	{
		throw new BadMethodCallException(sprintf('%s::%s() not defined', get_class($this), __FUNCTION__ ));
	}

	public function createHtml()
	{
		throw new BadMethodCallException(sprintf('%s::%s() not defined', get_class($this), __FUNCTION__ ));
	}

}
