<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

echo '<pre>';

require_once ("bootstrap.php");

class a extends ArrayObject
{
	const ARRAY_PROP_BOTH = 3;

	public $p = null;
	public $c = 1;

	function __construct($input)
	{
		$this->setFlags(self::ARRAY_PROP_BOTH);
		$this->exchangeArray($input);
	}

}

class b extends ArrayObject
{
	const ARRAY_PROP_BOTH = 3;

	public $p = null;
	public $c = 1;

	function __construct($input)
	{
		$this->setFlags(self::ARRAY_PROP_BOTH);
		$this->append($input);
	}

}

class c extends ArrayObject
{
	const ARRAY_PROP_BOTH = 3;

	public $p = null;
	public $c = 1;

	function __construct($input)
	{
		$this->setFlags(self::ARRAY_PROP_BOTH);
		$this->exchangeArray($input);
	}

	function exchangeArray($input)
	{
		$array = parent::exchangeArray($input);

		foreach (get_class_vars(get_class($this)) as $k => $v)
		{
			if (isset($this[$k]))
			{
				$this->$k = &$this[$k];
			}
		}

		return $array;
	}

}


$t[] = new a(array('p' => 1, 'c' => 0, 't' => 3));
$t[] = new b(array('p' => 1, 'c' => 0, 't' => 3));
$t[] = new c(array('p' => 1, 'c' => 0, 't' => 3));

foreach ($t as $a)
{
	var_dump($a);

	foreach ($a as $k => $v)
	{
		echo '---------<br>';
		debug($k, $a->$k, $a[$k], $a->$k, $a[$k]);
	}
}

debug($t[0]->c);

exit();

echo LOG_BATTLE_RANK.'1336662831161520.dat.yml';

$data = HOF_Class_Yaml::parse(LOG_BATTLE_NORMAL.'1336662831161520.dat.yml');

var_dump($data);

exit();

$option = array(
			/*
			"always"=> "Always",
			"never"	=> "Never",
			"life25"	=> "If life more than 25%",
			"life50"	=> "If life more than 50%",
			"life75"	=> "If life more than 75%",
			"prob25"	=> "Probability of 25%",
			"prpb50"	=> "Probability of 50%",
			"prob75"	=> "Probability of 75%",
			*/
			"always" => "必ず守る",
			"never" => "守らない",
			"life25" => "体力が 25%以上なら 守る",
			"life50" => "体力が 50%以上なら 守る",
			"life75" => "体力が 75%以上なら 守る",
			"prob25" => "25%の確率で 守る",
			"prpb50" => "50%の確率で 守る",
			"prob75" => "75%の確率で 守る",

			);

foreach ($option as $k => $v)
{
	$file = HOF_Class_Data::_filename('guard', $k);

	$data = HOF_Class_Yaml::load($file);

	$data['no'] = $k;
	$data['info']['desc'] = $v;

	$data['_i18n']['ja']['desc'] = $data['info']['desc.ja'];
	$data['_i18n']['en']['desc'] = $data['info']['desc.en'];

	unset($data['info']['desc.ja']);
	unset($data['info']['desc.en']);

	HOF_Class_Yaml::save($file, $data);

}

exit();

var_export(HOF_Class_Icon::getRandNo(HOF_Class_Icon::IMG_CHAR, 'ori_002'));



/*
foreach(glob(BASE_PATH_USER.'admin/*.dat') as $file)
{

	$fp = HOF_Class_File::fplock_file($file);
	$data = HOF_Class_File::ParseFileFP($fp);

	foreach (array('party_memo', 'party_rank', 'skill') as $k)
	{
		if (isset($data[$k]))
		{
			$data[$k] = (is_array($data[$k]) ? $data[$k] : explode("<>", $data[$k]));
		}
	}

	if (isset($data['Pattern']))
	{
		$Pattern = explode("|", $data['Pattern']);
		$judge = explode("<>", $Pattern["0"]);
		$quantity = explode("<>", $Pattern["1"]);
		$action = explode("<>", $Pattern["2"]);

		$data['Pattern'] = array(
			'judge' => (array)$judge,
			'quantity' => (array)$quantity,
			'action' => (array)$action,
		);
	}

	$file = str_replace('.dat', '.yml', $file);

	HOF_Class_Yaml::save($file, $data);

}
*/

function in_num($n, $min, $max)
{
	return ($n >= $min && $n <= $max);
}

/*
$_class_exists = array(
	'HOF',
	'HOF_Model_Char',
	'HOF_Class_Data',
	'Symfony_Component_Yaml_Yaml',
	'HOF_Class_Yaml',
	'HOF_Class_Battle_Style',
	'HOF_Class_Char_Mon_Union',
);

foreach($_class_exists as $_class)
{
	var_dump(array(
		$_class => class_exists($_class)
	));
}
*/

$job = array(
	// ここでしか必要無いので 職データには書きません。
	100 => "戦士系基本職。<br />そこそこ耐えて、攻撃もそこそこ。",
	101 => "戦士系上級職。<br />防御も攻撃も一回り強くなる。",
	102 => '戦士系上級職。<br />攻撃に特化した戦士。<br />自分の体力を犠牲に強力な技が使える。<br /><a href="?manual#sacrier">Sacrierの攻撃について</a>',
	103 => "戦士系上級職。<br />相手の魔力を奪ったりする、やや変則的な戦士。",
	200 => "魔法系基本職。<br />撃たれ弱いが強い魔法が使える。",
	201 => "魔法系上級職。<br />さらに強力な魔法が使えるようになる。",
	202 => "魔法系上級職。<br />時間はかかるが強力な召喚獣を呼べる。",
	203 => "魔法系上級職。<br />相手の能力を下げたり、ゾンビを作ったり出来る。<br />毒も扱える。",
	300 => "聖職基本職。<br />味方のHP,SPの回復ができる。",
	301 => "聖職上級職。<br />味方の能力値も上げれるようになる。",
	302 => "聖職上級職。<br />特殊な支援能力を持っている。",
	400 => "弓系基本職。<br />相手の前衛に影響されずに攻撃できる。",
	401 => "弓系上級職。<br />さらに強力な攻撃が可能。",
	402 => "弓系上級職。<br />素早い召喚と召喚獣の強化が得意。",
	403 => "弓系上級職。<br />毒の扱いに長けた職業。",
	);

$map[1000] = '必ず実行される';

$map[1001] = '必ず(パス)飛ばされる';

$map[1100] = $map[1101] = 'HPが○○(%)以上/以下
だった場合実行される。';

$map[1121] = 'HPが○○(%)以下のキャラが
一人以上居た場合実行。';

$map[1125] = $map[1126] = '平均HPが○○(%)以上/以下
なら実行。';

$map[1200] = $map[1201] = 'SPが○○(%)以上/以下
だった場合実行される。';


foreach(array('job') as $idx)
{
	$regex = HOF_Class_Data::_filename($idx, '*');
	$regex = '/^'.str_replace('\*', '(.+)', preg_quote($regex, '/')).'$/i';

	$last = null;

	$datas = array();

	$files = array();

	/*
	foreach(glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$no = preg_replace($regex, '$1', $file);
		$files[$no] = $file;
	}

	ksort($files, SORT_STRING);

	print_r($files);
	*/
	$files = glob(HOF_Class_Data::_filename($idx, '*'));

	//error_reporting(E_ALL);

	foreach($files as $file)
	{
		$no = preg_replace($regex, '$1', $file);

		echo "$idx : $no : $file\n";

		$data = HOF_Class_Yaml::load($file);

		var_dump($data['info']['desc']);

		if ($job[$data['no']])
		{
			$data['info']['desc'] = str_replace('<br />', "\n", $job[$data['no']]);
		}

		var_dump($data['info']['desc']);

		/*

		$_no = substr($no, -1);
		$_k = substr($no, 0, strlen($no) - strlen((string)$_no));
		if ($no == "horh")
		{
			$data['trigger']['time'][] = array(
				'H' => 2,
				'i' => 5,
			);
		}
		*/

		/*
		if ($_k == 'ac')
		{
			$data['trigger']['item'][8000 + $_no] = 1;
		}
		elseif ($_k == 'snow')
		{
			$data['trigger']['item'][8009 + $_no] = 1;
		}
		*/

		/*
		if ($data['trigger']['item'])
		{
			$item = $data['trigger']['item'];
			$data['trigger']['item'] = array();
			$data['trigger']['item'][] = $item;
		}


		if (is_numeric($_no) && $_no > 0 && $datas[$_k.'0'])
		{
			$datas[$_k.'0']['subs']['land'][$no] = $data;
		}

		$_last['_tmp']['i'] = $_no;
		$_last['_tmp']['k'] = $_k;
		$_last['data'] = $data;

		$last = $data;

		$datas[(string)$data['no']] = $data;
		*/

		HOF_Class_Yaml::save($file, $data);
	}

	if (0 && !empty($datas))
	{
		foreach($datas as $no => $data)
		{
			HOF_Class_Yaml::save(HOF_Class_Data::_filename($idx, (string)$no), $data);
		}
	}

}

/*
for ($i=100; $i<=500; $i++)
{
	$ret = HOF_Model_Data::getJobData($i);

	if (empty($ret)) continue;

	$file = 'test/job.' . $i . '.yml';

	echo "$file<br>";
	if ($ret['name']) echo "$ret[name]<br>";

	$data = array();
	$data['no'] = $i;

	$data += $ret;

	$datas[$i] = $data;

	HOF_Class_Yaml::save($file, $data);
}
*/

/*
echo 1233;

$_class_exists = array(
	'HOF',
	'HOF_Model_Char',
	'HOF_Class_Data',
	'HOF_Class_Yaml',
	'HOF_Class_Battle_Style',
	'HOF_Class_Char_Mon_Union',
);

foreach($_class_exists as $_class)
{
	var_dump(array(
		$_class => class_exists($_class)
	));
}
*/

//var_dump(HOF_Autoloader::$error);

//$c = new HOF_Class_Data;
//
//var_dump($c);

//var_dump(HOF_Model_Char::getInstance());
//
//var_dump(HOF_Model_Char::newBaseChar(1, array('gender' => $j)));

echo '<br>Done.';