<?
//function DecideJudge($number,$My,$MyTeam,$EnemyTeam,$classBattle) {
function DecideJudge($number,$My,$classBattle) {

	//Ƚ��˻��Ѥ��롡�������ѿ������󡡤Ȥ���
	//�׻������ꤹ�롣
	if($My->team == TEAM_0) {
		$MyTeam	= $classBattle->team0;
		$EnemyTeam	= $classBattle->team1;
		$MyTeamMC	= $classBattle->team0_mc;
		$EnemyTeamMC	= $classBattle->team1_mc;
	} else {
		$MyTeam	= $classBattle->team1;
		$EnemyTeam	= $classBattle->team0;
		$MyTeamMC	= $classBattle->team1_mc;
		$EnemyTeamMC	= $classBattle->team0_mc;
	}

	$Judge		= $My->judge["$number"];
	$Quantity	= $My->quantity["$number"];

	switch($Judge) {

		case 1000:// ɬ��
			return true;
		case 1001:// �ѥ�
			return false;

//------------------------ HP
		case 1100:// ��ʬ��HP ��**(%)�ʾ�
			$hpp	= $My->HpPercent();
			if($Quantity <= $hpp) return true;
			break;
		case 1101:// ��ʬ��HP ��**(%)�ʲ�
			$hpp	= $My->HpPercent();
			if($hpp <= $Quantity) return true;
			break;

		case 1105:// ��ʬ��HP ��**�ʾ�
			$hp		= $My->HP;
			if($Quantity <= $hp) return true;
			break;
		case 1106:// ��ʬ��HP ��**�ʲ�
			$hp		= $My->HP;
			if($hp <= $Quantity) return true;
			break;

		case 1110:// ����HP ��**�ʾ�
			$mhp		= $My->MAXHP;
			if($Quantity <= $mhp) return true;
			break;
		case 1111:// ����HP ��**�ʲ�
			$mhp		= $My->MAXHP;
			if($mhp <= $Quantity) return true;
			break;

		case 1121:// ̣���� HP��**(%)�ʲ��Υ���� �������
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->HpPercent() <= $Quantity)
					return true;
			}
			break;

		case 1125:// ̣���� ʿ��HP�� **(%)�ʾ�λ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
					$sum	+= $char->HpPercent();
					$cnt++;// ��¸�Ϳ�
			}
			$ave	= $sum/$cnt;
			if($Quantity <= $ave) return true;
			break;
		case 1126:// ̣���� ʿ��HP�� **(%)�ʲ��λ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
					$sum	+= $char->HpPercent();
					$cnt++;// ��¸�Ϳ�
			}
			$ave	= $sum/$cnt;
			if($ave <= $Quantity) return true;
			break;
//------------------------ SP
		case 1200:// ��ʬ��SP ��**(%)�ʾ�
			$spp	= $My->SpPercent();
			if($Quantity <= $spp) return true;
			break;

		case 1201:// ��ʬ��SP ��**(%)�ʲ�
			$spp	= $My->SpPercent();
			if($spp <= $Quantity) return true;
			break;

		case 1205:// ��ʬ��SP ��**�ʾ�
			$sp		= $My->SP;
			if($Quantity <= $sp) return true;
			break;
		case 1206:// ��ʬ��SP ��**�ʲ�
			$sp		= $My->SP;
			if($sp <= $Quantity) return true;
			break;

		case 1210:// ����SP ��**�ʾ�
			$msp		= $My->MAXSP;
			if($Quantity <= $msp) return true;
			break;
		case 1211:// ����SP ��**�ʲ�
			$msp		= $My->MAXSP;
			if($msp <= $Quantity) return true;
			break;

		case 1221:// ̣���� SP��**(%)�ʲ��Υ���� �������
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
				if($char->SpPercent() <= $Quantity)
					return true;
			}
			break;

		case 1225:// ̣���� ʿ��SP�� **(%)�ʾ�λ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
					
					$sum	+= $char->SpPercent();
					$cnt++;// ��¸�Ϳ�
			}
			// Divide by Zero
			if(!$cnt)
				break;
			$ave	= $sum/$cnt;
			if($Quantity <= $ave) return true;
			break;

		case 1226:// ̣���� ʿ��SP�� **(%)�ʲ��λ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
					$sum	+= $char->SpPercent();
					$cnt++;// ��¸�Ϳ�
			}
			// Divide by Zero
			if(!$cnt)
				break;
			$ave	= $sum/$cnt;
			if($ave <= $Quantity) return true;
			break;
//------------------------ STR
		case 1300:// ��ʬ��STR��** �ʾ�
			break;
		case 1301:// ��ʬ��STR��** �ʲ�
			break;
//------------------------ INT
		case 1310:// ��ʬ��INT��** �ʾ�
			break;
		case 1311:// ��ʬ��INT��** �ʲ�
			break;
//------------------------ DEX
		case 1320:// ��ʬ��DEX��** �ʾ�
			break;
		case 1321:// ��ʬ��DEX��** �ʲ�
			break;
//------------------------ SPD
		case 1330:// ��ʬ��SPD��** �ʾ�
			break;
		case 1331:// ��ʬ��SPD��** �ʲ�
			break;
//------------------------ LUK
		case 1340:// ��ʬ��LUK��** �ʾ�
			break;
		case 1341:// ��ʬ��LUK��** �ʲ�
			break;
//------------------------ ATK
		case 1350:// ��ʬ��ATK��** �ʾ�
			break;
		case 1351:// ��ʬ��ATK��** �ʲ�
			break;
//------------------------ MATK
		case 1360:// ��ʬ��MATK��** �ʾ�
			break;
		case 1361:// ��ʬ��MATK��** �ʲ�
			break;
//------------------------ DEF
		case 1370:// ��ʬ��DEF��** �ʾ�
			break;
		case 1371:// ��ʬ��DEF��** �ʲ�
			break;
//------------------------ MDEF
		case 1380:// ��ʬ��MDEF��** �ʾ�
			break;
		case 1381:// ��ʬ��MDEF��** �ʲ�
			break;
//------------------------ �Ϳ�(̣��)
		case 1400:// ̣������¸�Ԥ� *�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($Quantity <= $alive) return true;
			break;

		case 1401:// ̣������¸�Ԥ� *�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($alive <= $Quantity) return true;
			break;

		case 1405:// ̣���λ�Ԥ� *�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($Quantity <= $dead) return true;
			break;

		case 1406:// ̣���λ�Ԥ� *�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($dead <= $Quantity) return true;
			break;

		case 1410:// ̣�������Ҥ���¸�Ԥ� *�Ͱʾ�
			$front_alive	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD && $char->position == FRONT)
					$front_alive++;
			}
			if($Quantity <= $front_alive) return true;
			break;

//------------------------ �Ϳ�(Ũ)
		case 1450:// ������¸�Ԥ� *�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($Quantity <= $alive) return true;
			break;

		case 1451:// ������¸�Ԥ� *�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($alive <= $Quantity) return true;
			break;

		case 1455:// ���λ�Ԥ� *�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($Quantity <= $dead) return true;
			break;

		case 1456:// ���λ�Ԥ� *�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($dead <= $Quantity) return true;
			break;

//------------------------ ���㡼��+�Ӿ�
		case 1500:// ���㡼����Υ���餬 *�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($Quantity <= $charge) return true;
			break;

		case 1501:// ���㡼����Υ���餬 *�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($charge <= $Quantity) return true;
			break;

		case 1505:// �Ӿ���Υ���餬 *�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($Quantity <= $cast) return true;
			break;

		case 1506:// �Ӿ���Υ���餬 *�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$cast++;
			}
			if($cast <= $Quantity) return true;
			break;

		case 1510:// ���㡼�����Ӿ���Υ���餬 *�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($Quantity <= $expect) return true;
			break;

		case 1511:// ���㡼�����Ӿ���Υ���餬 *�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($expect <= $Quantity) return true;
			break;

//------------------------ ���㡼��+�Ӿ�(Ũ)
		case 1550:// ���㡼�������꤬ *�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($Quantity <= $charge) return true;
			break;

		case 1551:// ���㡼�������꤬ *�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($charge <= $Quantity) return true;
			break;

		case 1555:// �Ӿ������꤬ *�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($Quantity <= $cast) return true;
			break;

		case 1556:// �Ӿ������꤬ *�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($cast <= $Quantity) return true;
			break;

		case 1560:// ���㡼�����Ӿ������꤬ *�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($Quantity <= $expect) return true;
			break;

		case 1561:// ���㡼�����Ӿ������꤬ *�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($expect <= $Quantity) return true;
			break;

//------------------------ ��
		case 1600:// ��ʬ���Ǿ���
			if($My->STATE === POISON) return true;
			break;

		case 1610:// �Ǿ��֤�̣���� **�Ͱʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($Quantity <= $poison) return true;
			break;

		case 1611:// �Ǿ��֤�̣���� **�Ͱʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($poison <= $Quantity) return true;
			break;

		case 1612:// �Ǿ��֤�̣���� **% �ʾ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Quantity <= $Rate) return true;
			break;

		case 1613:// �Ǿ��֤�̣���� **% �ʲ�
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Rate <= $Quantity) return true;
			break;
//------------------------ ��(Ũ)
		case 1615:// �Ǿ��֤���꤬ **�Ͱʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($Quantity <= $poison) return true;
			break;

		case 1616:// �Ǿ��֤���꤬ **�Ͱʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($poison <= $Quantity) return true;
			break;

		case 1612:// �Ǿ��֤���꤬ **% �ʾ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Quantity <= $Rate) return true;
			break;

		case 1613:// �Ǿ��֤���꤬ **% �ʲ�
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Rate <= $Quantity) return true;
			break;
//------------------------ ����
		case 1700:// ��ʬ������
			if($My->POSITION == FRONT) return true;
			break;

		case 1701:// ��ʬ������
			if($My->POSITION == BACK) return true;
			break;

		case 1710:// ̣���� ����**�Ͱʾ�
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity <= $front) return true;
			break;

		case 1711:// ̣���� ����**�Ͱʲ�
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front <= $Quantity) return true;
			break;

		case 1712:// ̣���� ����**��
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front == $Quantity) return true;
			break;

		case 1715:// ̣���� ����**�Ͱʾ�
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity <= $back) return true;
			break;

		case 1716:// ̣���� ����**�Ͱʲ�
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back <= $Quantity) return true;
			break;

		case 1717:// ̣���� ����**��
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back == $Quantity) return true;
			break;

//------------------------ ����(Ũ)
		case 1750:// ���� ����**�Ͱʾ�
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity <= $front) return true;
			break;

		case 1751:// ���� ����**�Ͱʲ�
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front <= $Quantity) return true;
			break;

		case 1752:// ���� ����**��
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity == $front) return true;
			break;

		case 1755:// ���� ����**�Ͱʾ�
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity <= $back) return true;
			break;

		case 1756:// ���� ����**�Ͱʲ�
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back <= $Quantity) return true;
			break;

		case 1757:// ���� ����**��
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity == $back) return true;
			break;

//------------------------ ����
		case 1800:// ̣���� ��������餬 **ɤ�ʾ�
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($Quantity <= $summon) return true;
			break;

		case 1801:// ̣���� ��������餬 **ɤ�ʲ�
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon <= $Quantity) return true;
			break;

		case 1805:// ̣���� ��������餬 **ɤ
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon == $Quantity) return true;
			break;

//------------------------ ����(Ũ)
		case 1820:// ���� ��������餬 **ɤ�ʾ�
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($Quantity <= $summon) return true;
			break;

		case 1821:// ���� ��������餬 **ɤ�ʲ�
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon <= $Quantity) return true;
			break;

		case 1825:// ���� ��������餬 **ɤ
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon == $Quantity) return true;
			break;

//------------------------ ��ˡ��
		case 1840:// ̣������ˡ�ؤο��� **�İʾ�
			if($Quantity <= $MyTeamMC)
				return true;
			break;
		case 1841:// ̣������ˡ�ؤο��� **�İʲ�
			if($MyTeamMC <= $Quantity)
				return true;
			break;
		case 1845:// ̣������ˡ�ؤο��� **��
			if($Quantity == $MyTeamMC)
				return true;
			break;
//------------------------ ��ˡ��(Ũ)
		case 1850:// ������ˡ�ؤο��� **�İʾ�
			if($Quantity <= $EnemyTeamMC)
				return true;
			break;
		case 1851:// ������ˡ�ؤο��� **�İʲ�
			if($EnemyTeamMC <= $Quantity)
				return true;
			break;
		case 1855:// ������ˡ�ؤο��� **��
			if($Quantity == $EnemyTeamMC)
				return true;
			break;

//------------------------ �����ư���
		case 1900:// ��ʬ�ι�ư����� **��ʾ�
			if(($Quantity-1) <= $My->ActCount) return true;
			break;

		case 1901:// ��ʬ�ι�ư����� **��ʲ�
			if($My->ActCount <= ($Quantity-1)) return true;
			break;

		case 1902:// ��ʬ�ι�ư����� **����
			if($My->ActCount == ($Quantity-1)) return true;
			break;

//------------------------ �������
		case 1920:// **�����ɬ��
			if($My->JdgCount[$number] < $Quantity) return true;
			break;

//------------------------ ��Ψ
		case 1940:// **%�γ�Ψ��
			$prob	= mt_rand(1,100);
			if($prob <= $Quantity) return true;
			break;


//------------------------ �ü��Ƚ��
		case 9000:// ���������Lv**�ʾ夬��롣
			foreach($EnemyTeam as $char) {
				if($Quantity <= $char->level)
					return true;
			}
			break;
	}
}
//////////////////////////////////////////////////
//	SP�ˤ����ѤǤ�����(?)
function &FuncTeamHpSpRate(&$TeamHpRate,$NO) {
	foreach($TeamHpRate as $key => $Rate) {
		if($Rate <= $NO)
			$target[]	= &$MyTeam[$key];
	}
	return $target ? $target : false;
}
?>