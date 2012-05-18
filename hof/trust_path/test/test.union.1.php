<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$dir = __DIR__.'/union/';

foreach (HOF_Class_File::glob(UNION) as $file)
{
	if ($_data = HOF_Class_File::ParseFile($file))
	{
		$data = array();

		$mon = HOF_Model_Char::getBaseMonster($_data["MonsterNumber"]);

		$data['no'] = substr(basename($file), 0, 4);
		$data['name'] = $mon['UnionName'];

		$data['data']['team']['name'] = $mon['UnionName'];
		$data['data']['team']['servant'] = $mon["Slave"];

		$data['data']['base'] = array(
			'type' => 'mon',
			'no' => $_data["MonsterNumber"],
		);

		$data['data_ex']['name'] = $mon['UnionName'];
		$data['data_ex']['level'] = $mon['level'];
		$data['data_ex']['img'] = $mon['img'];

		$data['data_ex']['land'] = $mon['land'];
		$data['data_ex']['cycle'] = $mon["cycle"];

		$data['data']['conditions']['lv_limit'] = $mon['LevelLimit'];

		/*
		$data['data']["last_battle"] = 0;
		$data['hp'] = 0;
		$data['sp'] = 0;
		*/

		/*
		debug($data, $_data, $mon);
		exit();
		*/

		HOF_Class_File::mkdir(__DIR__.'/union/');

		HOF_Class_Yaml::save($dir.'union.'.$data['no'].'.yml', $data);
	}
}