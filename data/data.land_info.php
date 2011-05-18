<?php
/*
	通常戦闘における場所の情報
	基本的情報、出現する敵等

	name0 = 未使用
	proper = 未使用
	land = 土地の画像に対応する
	
	*** 敵の出現確率の設定について
	$monster = array(
	敵番号 = array("確率",1=表示 0=隠し敵);
	)

	確率の合計は気にしなくていい。
	出現確率 = ("確率"/全体の確率合計)
*/
function LandInformation($land) {
	switch($land) {
		case "gb0":
			$land	= array(
			"name"	=> "ゴブリンと遊ぶ(最弱)",
			"name0"	=> "Goblin Training",
			"land"	=> "grass",
			"proper"	=> "Lv1",
			);
			$monster	= array(
			1000	=> array(300,1),
			1001	=> array(300,1),
			); break;
		case "gb1":
			$land	= array(
			"name"	=> "ちょっと強いゴブリン",
			"name0"	=> "The Goblins",
			"land"	=> "grass",
			"proper"	=> "Lv1-5",
			);
			$monster	= array(
			1000	=> array(300,1),
			1001	=> array(300,1),
			1002	=> array(150,1),
			1003	=> array(150,1),
			1004	=> array(100,1),
			1005	=> array(100,1),
			1006	=> array(100,1),
			1007	=> array(100,1),
			); break;
		case "gb2":
			$land	= array(
			"name"	=> "ゴブリンの戦士達",
			"name0"	=> "The Goblin Warriors",
			"land"	=> "grass",
			"proper"	=> "Lv3-8",
			);
			$monster	= array(
			1005	=> array(100,1),
			1006	=> array(100,1),
			1007	=> array(100,1),
			1008	=> array(100,1),
			1009	=> array(100,1),
			); break;
		case "ac0":
			$land	= array(
			"name"	=> "古の洞窟",
			"name0"	=> "TheAncientCave",
			"land"	=> "cave",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1010	=> array(0,1),// 実際には出現しないが
			1011	=> array(0,1),// 1012がその代わり
			1012	=> array(500,0),
			1013	=> array(150,1),
			1014	=> array(150,1),
			1015	=> array(150,1),
			1016	=> array(100,0),
			1017	=> array(50,0),
			); break;
		case "ac1":
			$land	= array(
			"name"	=> "古の洞窟(B2)",
			"name0"	=> "TheAncientCave2F",
			"land"	=> "cave",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1018	=> array(100,1),
			1019	=> array(100,1),
			1020	=> array(100,1),
			1021	=> array(100,1),
			1022	=> array(30,1),
			); break;
		case "ac2":
			$land	= array(
			"name"	=> "古の洞窟(B3)",
			"name0"	=> "TheAncientCave3F",
			"land"	=> "cave",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1023	=> array(400,1),
			1024	=> array(300,1),
			1025	=> array(35,0),
			); break;
		case "ac3":
			$land	= array(
			"name"	=> "古の洞窟(B4)",
			"name0"	=> "TheAncientCave4F",
			"land"	=> "cave",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1027	=> array(20,1),
			1028	=> array(80,1),
			//1029	=> array(100,1),
			); break;
		case "ac4":
			$land	= array(
			"name"	=> "古の洞窟(B5)",
			"name0"	=> "TheAncientCave5F",
			"land"	=> "cave",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1030	=> array(150,1),
			1031	=> array(150,1),
			1032	=> array(150,1),
			1033	=> array(200,1),
			1034	=> array(70,1),
			1035	=> array(10,0),
			); break;

		case "sea0":
			$land	= array(
			"name"	=> "海",
			"name0"	=> "",
			"land"	=> "sea",
			"proper"	=> "99-99",
			);
			$monster	= array(
			1060	=> array(7,0),
			1061	=> array(100,1),
			1062	=> array(100,1),
			1063	=> array(100,1),
			1064	=> array(100,1),
			1065	=> array(100,1),
			1066	=> array(5,0),
			); break;
		case "sea1":
			$land	= array(
			"name"	=> "海(西海岸)",
			"name0"	=> "",
			"land"	=> "sea",
			"proper"	=> "99-99",
			);
			$monster	= array(
			1065	=> array(100,1),
			); break;

		case "ocean0":
			$land	= array(
			"name"	=> "海中",
			"name0"	=> "",
			"land"	=> "ocean",
			"proper"	=> "99-99",
			);
			$monster	= array(
			1067	=> array(8,0),
			1068	=> array(100,1),
			1069	=> array(100,1),
			1070	=> array(100,1),
			1071	=> array(100,1),
			); break;

		case "sand0":
			$land	= array(
			"name"	=> "砂漠",
			"name0"	=> "",
			"land"	=> "sand",
			"proper"	=> "99-99",
			);
			$monster	= array(
			1083	=> array(100,1),
			1084	=> array(7,0),
			1085	=> array(100,1),
			1086	=> array(100,1),
			1087	=> array(100,1),
			); break;

		case "mt0":
			$land	= array(
			"name"	=> "火山ふもと",
			"name0"	=> "",
			"land"	=> "mount",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1072	=> array(100,1),
			1073	=> array(100,1),
			1078	=> array(100,1),
			1082	=> array(5,0),
			); break;
		case "volc0":
			$land	= array(
			"name"	=> "火山(中腹)",
			"name0"	=> "",
			"land"	=> "lava",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1073	=> array(80,1),
			1074	=> array(100,1),
			1077	=> array(100,1),
			1080	=> array(100,1),
			1088	=> array(10,0),
			); break;
		case "volc1":
			$land	= array(
			"name"	=> "火山(頂上)",
			"name0"	=> "",
			"land"	=> "lava",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1074	=> array(60,1),
			1075	=> array(170,1),
			1077	=> array(120,1),
			1080	=> array(100,1),
			1081	=> array(8,0),
			1076	=> array(3,0),
			); break;

		case "swamp0":
			$land	= array(
			"name"	=> "沼",
			"name0"	=> "",
			"land"	=> "swamp",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1053	=> array(100,1),
			1054	=> array(100,1),
			1055	=> array(100,1),
			1056	=> array(100,1),
			1057	=> array(100,1),
			1058	=> array(20,0),
			); break;
		case "swamp1":
			$land	= array(
			"name"	=> "集落?",
			"name0"	=> "",
			"land"	=> "swamp",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1053	=> array(70,1),
			1050	=> array(100,1),
			1051	=> array(150,1),
			1052	=> array(100,1),
			1059	=> array(8,0),
			); break;

		case "snow0":
			$land	= array(
			"name"	=> "滴凍山ふもと",
			"name0"	=> "FrostyMountain(foot)",
			"land"	=> "snow",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1036	=> array(100,1),
			1037	=> array(100,1),
			1038	=> array(100,1),
			1039	=> array(100,1),
			); break;
		case "snow1":
			$land	= array(
			"name"	=> "滴凍山(中腹)",
			"name0"	=> "FrostyMountain(HalfWay)",
			"land"	=> "snow",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1040	=> array(100,1),
			1041	=> array(100,1),
			1042	=> array(100,1),
			1043	=> array(100,1),
			1044	=> array(40,1),
			1045	=> array(15,0),
			1046	=> array(10,0),
			); break;
		case "snow2":
			$land	= array(
			"name"	=> "滴凍山(頂上)",
			"name0"	=> "FrostyMountain(Top)",
			"land"	=> "snow",
			"proper"	=> "Lv??",
			);
			$monster	= array(
			1089	=> array(80,1),
			1090	=> array(80,1),
			1044	=> array(30,1),
			1047	=> array(100,1),
			1048	=> array(60,1),
			1049	=> array(5,0),
			1046	=> array(10,0),
			); break;





		case "des01":
			$land	= array(
			"name"	=> "略奪者の砂漠",
			"name"	=> "Plunderer's Sandland",
			"land"	=> "sand",
			"proper"	=> "Lv5-10",
			);
			$monster	= array(
			1005	=> array(100,1),
			1006	=> array(100,1),
			1007	=> array(100,1),
			1008	=> array(100,1),
			1009	=> array(100,1),
			); break;
		case "plund01":
			$land	= array(
			"name"	=> "賊の巣窟",
			"name0"	=> "Plunderer's Nest",
			"land"	=> "sand",
			"proper"	=> "Lv10-15",
			);
			$monster	= array(
			1005	=> array(100,1),
			1006	=> array(100,1),
			1007	=> array(100,1),
			1008	=> array(100,1),
			1009	=> array(100,1),
			); break;
		case "blow01":
			$land	= array(
			"name"	=> "ヒルズブロウ地区",
			"name0"	=> "TheBlowHills",
			"land"	=> "aband",
			"proper"	=> "Lv20-30",
			);
			$monster	= array(
			1018	=> array(100,1),
			1019	=> array(100,1),
			1020	=> array(100,1),
			1021	=> array(100,1),
			1022	=> array(30,1),
			); break;
		case "horh":
			$land	= array(
			"name"	=> "ヘブンオアヘル",
			"name0"	=> "Heaven or Hell",
			"land"	=> "sea",
			"proper"	=> "Lv99",
			);
			$monster	= array(
			5100	=> array(100,1),
			5101	=> array(100,1),
			5102	=> array(100,1),
			5103	=> array(100,1),
			5104	=> array(100,1),
			); break;
	}

	return array($land,$monster);
}
?>