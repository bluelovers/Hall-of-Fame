<?php

/**
 * @author bluelovers
 * @copyright 2012
 *
 * @author http://www.talkphp.com/script-giveaway/1905-gravatar-wrapper-class.html
 */

/**
 * @see https://en.gravatar.com/site/implement/images/
 * @see http://en.gravatar.com/site/implement/images/php/
 */
class Sco_Api_Avatar_Gravatar implements Sco_Api_Avatar_Interface
{

	const SITE = 'http://www.gravatar.com/';
	const SITE_SECURE = 'https://secure.gravatar.com/';

	//const GRAVATAR_SITE_URL = 'avatar.php?gravatar_id=%ssize=%sdefault=%srating=%s&default=%s';
	const SITE_URL = 'avatar/%s?s=%s&d=%s&r=%s&f=%s';

	const DEFAULT_DEF = '';

	/**
	 * do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found)
	 */
	const DEFAULT_404 = 404;
	/**
	 * (mystery-man) a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
	 */
	const DEFAULT_MM = 'mm';
	/**
	 * a geometric pattern based on an email hash
	 */
	const DEFAULT_IDENTICON = 'identicon';
	/**
	 * a generated 'monster' with different colors, faces, etc
	 */
	const DEFAULT_MONSTERID = 'monsterid';
	/**
	 * generated faces with differing features and backgrounds
	 */
	const DEFAULT_WAVATAR = 'wavatar';
	/**
	 * awesome generated, 8-bit arcade-style pixelated faces
	 */
	const DEFAULT_RETRO = 'retro';

	const SIZE_80 = 80;

	/**
	 * suitable for display on all websites with any audience type.
	 */
	const RATING_G = 'g';
	/**
	 * may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence.
	 */
	const RATING_PG = 'pg';
	/**
	 * may contain such things as harsh profanity, intense violence, nudity, or hard drug use.
	 */
	const RATING_R = 'r';
	/**
	 * may contain hardcore sexual imagery or extremely disturbing violence.
	 */
	const RATING_X = 'x';

	const YES = 'y';
	const NO = 'n';

	protected $m_szEmail;

	protected $m_iSize = self::SIZE_80;
	protected $m_szImage = self::DEFAULT_DEF;
	protected $m_szRating = self::RATING_G;

	protected $m_force_default = self::NO;
	protected $m_secure_requests = false;

	public function __construct($email, $s = null, $d = null, $r = null, $f = null)
	{
		$this->setEmail($email);

		$s !== null && $this->setSize($s);
		$d !== null && $this->setDefaultImage($d);
		$r !== null && $this->setRating($r);
		$f !== null && $this->setDefaultImageForce($f);
	}

	/**
	 * @return Sco_Api_Avatar_Gravatar
	 */
	public static function newInstance($email, $s = null, $d = null, $r = null, $f = null)
	{
		return new self($email, $s, $d, $r, $f);
	}

	public function __toString()
	{
		return (string )$this->getAvatar();
	}

	public function getAvatar()
	{
		return sprintf($this->getSite() . self::SITE_URL, $this->m_szEmail, $this->m_iSize, $this->m_szImage, $this->m_szRating, $this->m_force_default);
	}

	public function setSecureRequests($flag = true)
	{
		$this->m_secure_requests = $flag;
		return $this;
	}

	/**
	 * What happens when an email address has no matching Gravatar image
	 *
	 * If you'd prefer to use your own default image (perhaps your logo, a funny face, whatever)
	 * , then you can easily do so by supplying the URL to an image in the d= or default= parameter.
	 * The URL should be URL-encoded to ensure that it carries across correctly
	 */
	public function setDefaultImage($szImage)
	{
		$this->m_szImage = urlencode($szImage);
		return $this;
	}

	public function setDefaultImageForce($flag = true)
	{
		$this->m_force_default = (bool)$flag ? self::YES : self::NO;
		return $this;
	}

	public function setDefaultImageAs404()
	{
		$this->m_szEmail = self::DEFAULT_404;
		return $this;
	}

	public function setDefaultImageAsDefault()
	{
		$this->m_szEmail = self::DEFAULT_DEF;
		return $this;
	}

	public function setDefaultImageAsMm()
	{
		$this->m_szEmail = self::DEFAULT_MM;
		return $this;
	}

	public function setDefaultImageAsMonsterId()
	{
		$this->m_szEmail = self::DEFAULT_MONSTERID;
		return $this;
	}

	public function setDefaultImageAsIdentIcon()
	{
		$this->m_szEmail = self::DEFAULT_IDENTICON;
		return $this;
	}

	public function setDefaultImageAsWavatar()
	{
		$this->m_szEmail = self::DEFAULT_WAVATAR;
		return $this;
	}

	public function setDefaultImageAsRetro()
	{
		$this->m_szEmail = self::DEFAULT_RETRO;
		return $this;
	}

	public function setEmail($szEmail)
	{
		$this->m_szEmail = md5(strtolower(trim($szEmail)));
		return $this;
	}

	/**
	 * By default, images are presented at 80px by 80px if no size parameter is supplied.
	 * You may request a specific image size
	 * , which will be dynamically delivered from Gravatar by using the s= or size= parameter
	 * and passing a single pixel dimension (since the images are square):
	 */
	public function setSize($iSize)
	{
		$this->m_iSize = urlencode((int)$iSize ? (int)$iSize : self::SIZE_80);
		return $this;
	}

	public function setRating($r)
	{
		$this->m_szRating = urlencode($r);
		return $this;
	}

	public function setRatingAsG()
	{
		$this->m_szRating = self::RATING_G;
		return $this;
	}

	public function setRatingAsPG()
	{
		$this->m_szRating = self::RATING_PG;
		return $this;
	}

	public function setRatingAsR()
	{
		$this->m_szRating = self::RATING_R;
		return $this;
	}

	public function setRatingAsX()
	{
		$this->m_szRating = self::RATING_X;
		return $this;
	}

	public function getSite()
	{
		return ($this->m_secure_requests ? self::SITE_SECURE : self::SITE);
	}
}
