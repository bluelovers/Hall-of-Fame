<?
function BaseCharStatus($no) {
	switch($no) {
		case "1":
$stat	= array(
"level"	=> "1",
"exp"	=> "0",
"maxhp"	=> "300",
"hp"	=> "300",
"maxsp"	=> "50",
"sp"	=> "50",
"str"	=> "10",
"int"	=> "2",
"dex"	=> "4",
"spd"	=> "4",
"luk"	=> "1",
"job"	=> "100",
"weapon"=> "1000",
"shield"=> "3000",
"armor"	=> "5000",
"skill"	=> array(1000,1001),
"Pattern"=> "1205<>1000|8<>0|1001<>1000",
"position"	=> "front",
"guard"	=> "always",
); break;
		case "2":
$stat	= array(
"level"	=> "1",
"exp"	=> "0",
"maxhp"	=> "150",
"hp"	=> "150",
"maxsp"	=> "100",
"sp"	=> "100",
"str"	=> "2",
"int"	=> "10",
"dex"	=> "5",
"spd"	=> "3",
"luk"	=> "1",
"job"	=> "200",
"weapon"=> "1700",
"armor"	=> "5200",
"skill"	=> array(1000,1002,3010),
"Pattern"=> "1206<>1000<>1000|20<>0<>0|3010<>1002<>1000",
"position"	=> "back",
"guard"	=> "never",
); break;
		case "3":
$stat	= array(
"level"	=> "1",
"exp"	=> "0",
"maxhp"	=> "200",
"hp"	=> "200",
"maxsp"	=> "80",
"sp"	=> "80",
"str"	=> "3",
"int"	=> "8",
"dex"	=> "5",
"spd"	=> "4",
"luk"	=> "1",
"job"	=> "300",
"weapon"=> "1700",
"armor"	=> "5200",
"skill"	=> array(1000,3000,3101),
"Pattern"=> "1121<>1000|70<>0|3000<>3101",
"position"	=> "back",
"guard"	=> "never",
); break;
		case "4":
$stat	= array(
"level"	=> "1",
"exp"	=> "0",
"str"	=> "2",
"int"	=> "2",
"dex"	=> "10",
"spd"	=> "6",
"luk"	=> "1",
"job"	=> "400",
"weapon"=> "2000",
"armor"	=> "5100",
"skill"	=> array(2300,2310),
"Pattern"=> "1205<>1000|28<>0|2310<>2300",
"position"	=> "back",
"guard"	=> "never",
); break;
	}

	$stat	+= array( "birth" => time().substr(microtime(),2,6) );
	return $stat;
}
?>