<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Crypto_HOF extends Sco_Crypto_Base
{

	protected function _salt($salt = null)
	{
		if ($salt === null)
		{
			$salt = substr(md5(Sco_Crypto_Salt::uniqid()), 0, 8);

			$salt = '$1$' . $salt . '$';
		}
		elseif (!preg_match('/^\$\d\$[0-9a-zA-Z]+\$/', $salt))
		{
			if (strpos($salt, '$') === false)
			{
				$salt = '$1$' . substr($salt, 0, 8) . '$';
			}
			else
			{
   				throw new InvalidArgumentException('BAD SALT: "'.$salt.'", should be like $1$12345678$.');
   			}
		}
		else
		{
			$s = explode('$', $salt);
			$salt = '$1$' . substr($s[2], 0, 8) . '$';
		}

		return $salt;
	}

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	public function encode($val)
	{
		return crypt($val, $this->salt);

		return substr(crypt($val, $this->salt), strlen($this->salt));
	}

}
