<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Item extends HOF_Class_Base_ObjectAttr
{
	public $id;

	/**
	 * 名称
	 */
	public $name;

	/**
	 * 画像
	 */
	public $icon;

	const UNKNOW_NAME = 'UnknowItem';

	/**
	 * 設定項目
	 * ---------------------------------------------
	 * "name"=>"名称",
	 * "type"=>"種類",
	 * "buy"=>"買値",
	 * "img"=>"画像",
	 * "atk"=>array(物理攻撃,魔法攻撃),
	 * "def"=>array(物理割?,物理減,魔法割?,魔法減),
	 * "dh"=> true,//両手武器か否か( "D"ouble"H"and )
	 * "handle"=>"数値",
	 * "need" => array("素材番号"=>数, ...),// 製作に必要なアイテム
	 * ---------------------------------------------
	 * type
	 * "Sword"	片手剣
	 * "TwoHandSword"	両手剣
	 * "Dagger"	短剣
	 * "Spear"	両手槍
	 * "Pike"	片手槍
	 * "Axe"	両手斧
	 * "Hatchet"片手斧
	 * "Wand"	片手杖
	 * "Staff"	両手杖
	 * "Mace"	鈍器(片手)
	 * "Bow"	弓
	 * "CrossBow"	石弓
	 *
	 * "Shield"	盾
	 * "MainGauche"	防御用短剣
	 * "Book"	本
	 *
	 * "Armor"	鎧
	 * "Cloth"	服
	 * "Robe"	衣
	 *
	 * "?"
	 *--------------------------------------------
	 * 追加オプション
	 * P_MAXHP
	 * M_MAXHP
	 * P_MAXSP
	 * M_MAXSP
	 * P_STR
	 * P_INT
	 * P_DEX
	 * P_SPD
	 * P_LUK
	 * P_SUMMON = 召還力強化
	 * P_PIERCE = array(物理,魔法),
	 *--------------------------------------------
	 */
	function __construct($no)
	{
		if (is_array($no))
		{
			$data = $no;
		}
		else
		{
			$data = HOF_Model_Data::getItemData($no, true);
		}

		$_source_data_ = $data;

		$data = HOF_Helper_Item::parseItemData($data, $data['id']);

		parent::__construct((array)$data);

		//$this->id = $this->id;
		$this->icon = $this->img;

		$this->_source_data_ = $_source_data_;

		return $this;
	}

	function exists()
	{
		return ($this->no) ? true : false;
	}

	function &newCopy($clone = false)
	{
		if ($clone)
		{
			$item = clone $this;
		}
		else
		{
			$item = new self($this->no);
		}

		return $item;
	}

	function &newInstance($no, $check = true)
	{
		if ($check && ($no instanceof self))
		{
			return $no;
		}

		return new self($no);
	}

	function id($over = false)
	{
		if (!$this->exists()) return 0;

		$var = $this->id;
		$var = printf("[%04s]", $var);

		return (string)$var;
	}

	function no()
	{
		$var = $this->no;
		$var = printf("[%04s]", $var);

		return (string)$var;
	}

	function __toString()
	{
		return $this->id();
	}

	/**
	 * jQuery style attr
	 */
	function attr($attr, $value = null)
	{
		if (is_string($attr))
		{
			if ($value === null)
			{
				return $this->$attr;
			}

			$this->$attr = $value;
		}
		elseif (is_array($attr))
		{
			foreach ($attr as $k => $v)
			{
				$this->$k = $v;
			}
		}

		return $this;
	}

	function icon($url = false, $true = false)
	{
		$icon = (!$true && $this->icon) ? $this->icon : $this->img;

		if ($url)
		{
			return HOF_Class_Icon::getImageUrl($icon, HOF_Class_Icon::IMG_ITEM);
		}
		else
		{
			return HOF_Class_Icon::getImage($icon, HOF_Class_Icon::IMG_ITEM);
		}
	}

	function html($amount = false, $text = true, $need = false)
	{
		$item = $this->toArray();
		$item['img'] = $this->icon(true);
		$item['name'] = $this->name();
		$item['handle'] = $this->handle();

		if (!$this->exists())
		{
			return '';
		}

		return self::ShowItemDetail($item, $amount, $text, $need);
	}

	function price_buy($format = false)
	{
		$price = (int)$this->buy;

		if ($format) return HOF_Helper_Global::MoneyFormat($price);

		return $price;
	}

	function price_sell($format = false)
	{
		$price = HOF_Helper_Item::ItemSellPrice($this);

		if ($format) return HOF_Helper_Global::MoneyFormat($price);

		return $price;
	}

	function amount($amount = null)
	{
		if ($amount !== null)
		{
			$this->amount = (int)$amount;

			return $this;
		}

		return (int)$this->amount;
	}

	function name()
	{
		$name = $this->name;

		if (!$name)
		{
			$name = self::UNKNOW_NAME.'#'.$this->id();
		}

		return $name;
	}

	function __call($func, $args)
	{
		if (property_exists($this, $func) || isset($this[$func]))
		{
			$val = $this->$func;

			return $val;
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method '.get_class($this).'::'.$func.'()');
		}
	}

	function handle()
	{
		if (!$this->exists()) return 0;

		$ret = max(1, $this->handle);

		return $ret;
	}

	/**
	 * アイテムの詳細を返す...ちょっと修正したいな。
	 */
	static function ShowItemDetail($item, $amount = false, $text = false, $need = false)
	{
		if (!$item) return false;

		$output = array();

		$output['item'] = $item;
		$output['amount'] = $amount;
		$output['need'] = $need;

		$content = HOF_Class_View::render(null, $output, 'layout/item.detail');

		if ($text)
		{
			return $content;
		}

		$content->output();

	}

}
