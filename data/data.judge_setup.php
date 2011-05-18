<?php
function LoadJudgeData($no) {
/*
	Ƚ�Ǻ���
	HP
	SP
	�Ϳ�(��ʬ������¸��)
	����(����)������
	��ʬ�ι�ư���
	�������
	���ξ���
	ñ��ʳ�Ψ
*/

	$Quantity	= '����';

	switch($no) {

		case 1000:// ɬ��
			$judge["exp"]	= "ɬ��";
			break;
		case 1001:// �ѥ�
			$judge["exp"]	= "����Ƚ�Ǥ�";
			break;
//------------------------ HP
		case 1099:
			$judge["exp"]	= "HP";
			$judge["css"]	= true;
			break;
		case 1100:// ��ʬ��HP ��{$Quantity}(%)�ʾ�
			$judge["exp"]	= "��ʬ��HP ��{$Quantity}(%)�ʾ�";
			break;
		case 1101:// ��ʬ��HP ��{$Quantity}(%)�ʲ�
			$judge["exp"]	= "��ʬ��HP ��{$Quantity}(%)�ʲ�";
			break;

		case 1105:// ��ʬ��HP ��{$Quantity}�ʾ�
			$judge["exp"]	= "��ʬ��HP ��{$Quantity}�ʾ�";
			break;
		case 1106:// ��ʬ��HP ��{$Quantity}�ʲ�
			$judge["exp"]	= "��ʬ��HP ��{$Quantity}�ʲ�";
			break;

		case 1110:// ����HP ��{$Quantity}�ʾ�
			$judge["exp"]	= "����HP ��{$Quantity}�ʾ�";
			break;
		case 1111:// ����HP ��{$Quantity}�ʲ�
			$judge["exp"]	= "����HP ��{$Quantity}�ʲ�";
			break;

		case 1121:// ̣���� HP��{$Quantity}(%)�ʲ��Υ���� �������
			$judge["exp"]	= "̣���� HP��{$Quantity}(%)�ʲ��Υ���� ������";
			break;

		case 1125:// ̣���� ʿ��HP�� {$Quantity}(%)�ʾ�λ�
			$judge["exp"]	= "̣���� ʿ��HP�� {$Quantity}(%)�ʾ�";
			break;
		case 1126:// ̣���� ʿ��HP�� {$Quantity}(%)�ʲ��λ�
			$judge["exp"]	= "̣���� ʿ��HP�� {$Quantity}(%)�ʲ�";
			break;
//------------------------ SP
		case 1199:
			$judge["exp"]	= "SP";
			$judge["css"]	= true;
			break;
		case 1200:// ��ʬ��SP ��{$Quantity}(%)�ʾ�
			$judge["exp"]	= "��ʬ��SP ��{$Quantity}(%)�ʾ�";
			break;
		case 1201:// ��ʬ��SP ��{$Quantity}(%)�ʲ�
			$judge["exp"]	= "��ʬ��SP ��{$Quantity}(%)�ʲ�";
			break;

		case 1205:// ��ʬ��SP ��{$Quantity}�ʾ�
			$judge["exp"]	= "��ʬ��SP ��{$Quantity}�ʾ�";
			break;
		case 1206:// ��ʬ��SP ��{$Quantity}�ʲ�
			$judge["exp"]	= "��ʬ��SP ��{$Quantity}�ʲ�";
			break;

		case 1210:// ����SP ��{$Quantity}�ʾ�
			$judge["exp"]	= "����SP ��{$Quantity}�ʾ�";
			break;
		case 1211:// ����SP ��{$Quantity}�ʲ�
			$judge["exp"]	= "����SP ��{$Quantity}�ʲ�";
			break;

		case 1221:// ̣���� SP��{$Quantity}(%)�ʲ��Υ���� �������
			$judge["exp"]	= "̣���� SP��{$Quantity}(%)�ʲ��Υ���� ������";
			break;

		case 1225:// ̣���� ʿ��SP�� {$Quantity}(%)�ʾ�λ�
			$judge["exp"]	= "̣���� ʿ��SP�� {$Quantity}(%)�ʾ�";
			break;
		case 1226:// ̣���� ʿ��SP�� {$Quantity}(%)�ʲ��λ�
			$judge["exp"]	= "̣���� ʿ��SP�� {$Quantity}(%)�ʲ�";
			break;
/*
//------------------------ STR
		case 1299:
			$judge["exp"]	= "STR";
			break;
		case 1300:// ��ʬ��STR��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��STR��{$Quantity} �ʾ�";
			break;
		case 1301:// ��ʬ��STR��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��STR��{$Quantity} �ʲ�";
			break;
//------------------------ INT
		case 1309:
			$judge["exp"]	= "INT";
			break;
		case 1310:// ��ʬ��INT��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��INT��{$Quantity} �ʾ�";
			break;
		case 1311:// ��ʬ��INT��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��INT��{$Quantity} �ʲ�";
			break;
//------------------------ DEX
		case 1319:
			$judge["exp"]	= "DEX";
			break;
		case 1320:// ��ʬ��DEX��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��DEX��{$Quantity} �ʾ�";
			break;
		case 1321:// ��ʬ��DEX��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��DEX��{$Quantity} �ʲ�";
			break;
//------------------------ SPD
		case 1329:
			$judge["exp"]	= "SPD";
			break;
		case 1330:// ��ʬ��SPD��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��SPD��{$Quantity} �ʾ�";
			break;
		case 1331:// ��ʬ��SPD��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��SPD��{$Quantity} �ʲ�";
			break;
//------------------------ LUK
		case 1339:
			$judge["exp"]	= "LUK";
			break;
		case 1340:// ��ʬ��LUK��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��LUK��{$Quantity} �ʾ�";
			break;
		case 1341:// ��ʬ��LUK��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��LUK��{$Quantity} �ʲ�";
			break;
//------------------------ ATK
		case 1349:
			$judge["exp"]	= "ATK";
			break;
		case 1350:// ��ʬ��ATK��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��ATK��{$Quantity} �ʾ�";
			break;
		case 1351:// ��ʬ��ATK��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��ATK��{$Quantity} �ʲ�";
			break;
//------------------------ MATK
		case 1359:
			$judge["exp"]	= "MATK";
			break;
		case 1360:// ��ʬ��MATK��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��MATK��{$Quantity} �ʾ�";
			break;
		case 1361:// ��ʬ��MATK��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��MATK��{$Quantity} �ʲ�";
			break;
//------------------------ DEF
		case 1369:
			$judge["exp"]	= "DEF";
			break;
		case 1370:// ��ʬ��DEF��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��DEF��{$Quantity} �ʾ�";
			break;
		case 1371:// ��ʬ��DEF��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��DEF��{$Quantity} �ʲ�";
			break;
//------------------------ MDEF
		case 1379:
			$judge["exp"]	= "MDEF";
			break;
		case 1380:// ��ʬ��MDEF��{$Quantity} �ʾ�
			$judge["exp"]	= "��ʬ��MDEF��{$Quantity} �ʾ�";
			break;
		case 1381:// ��ʬ��MDEF��{$Quantity} �ʲ�
			$judge["exp"]	= "��ʬ��MDEF��{$Quantity} �ʲ�";
			break;
*/
//------------------------ ����(̣��)
		case 1399:
			$judge["exp"]	= "����";
			$judge["css"]	= true;
			break;
		case 1400:// ̣������¸�Ԥ� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "̣������¸�Ԥ� {$Quantity}�Ͱʾ�";
			break;
		case 1401:// ̣������¸�Ԥ� {$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣������¸�Ԥ� {$Quantity}�Ͱʲ�";
			break;
		case 1405:// ̣���λ�Ԥ� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "̣���λ�Ԥ� {$Quantity}�Ͱʾ�";
			break;
		case 1406:// ̣���λ�Ԥ� {$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣���λ�Ԥ� {$Quantity}�Ͱʲ�";
			break;

		case 1410:// ̣�������Ҥ���¸�Ԥ� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "(������꤬)���Ҥ���¸�� {$Quantity}�Ͱʾ�";
			break;
//------------------------ ����(Ũ)
		case 1449:
			$judge["exp"]	= "����(Ũ)";
			$judge["css"]	= true;
			break;
		case 1450:// ������¸�Ԥ� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "������¸�Ԥ� {$Quantity}�Ͱʾ�";
			break;
		case 1451:// ������¸�Ԥ� {$Quantity}�Ͱʲ�
			$judge["exp"]	= "������¸�Ԥ� {$Quantity}�Ͱʲ�";
			break;
		case 1455:// ���λ�Ԥ� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "���λ�Ԥ� {$Quantity}�Ͱʾ�";
			break;
		case 1456:// ���λ�Ԥ� {$Quantity}�Ͱʲ�
			$judge["exp"]	= "���λ�Ԥ� {$Quantity}�Ͱʲ�";
			break;
//------------------------ ���㡼��+�Ӿ�
		case 1499:
			$judge["exp"]	= "���㡼��+�Ӿ�";
			$judge["css"]	= true;
			break;
		case 1500:// ���㡼����Υ���餬 {$Quantity}�Ͱʾ�
			$judge["exp"]	= "���㡼����Υ���餬 {$Quantity}�Ͱʾ�";
			break;
		case 1501:// ���㡼����Υ���餬 {$Quantity}�Ͱʲ�
			$judge["exp"]	= "���㡼����Υ���餬 {$Quantity}�Ͱʲ�";
			break;
		case 1505:// �Ӿ���Υ���餬 {$Quantity}�Ͱʾ�
			$judge["exp"]	= "�Ӿ���Υ���餬 {$Quantity}�Ͱʾ�";
			break;
		case 1506:// �Ӿ���Υ���餬 {$Quantity}�Ͱʲ�
			$judge["exp"]	= "�Ӿ���Υ���餬 {$Quantity}�Ͱʲ�";
			break;
		case 1510:// ���㡼�����Ӿ���Υ���餬 {$Quantity}�Ͱʾ�
			$judge["exp"]	= "���㡼�����Ӿ���Υ���餬 {$Quantity}�Ͱʾ�";
			break;
		case 1511:// ���㡼�����Ӿ���Υ���餬 {$Quantity}�Ͱʲ�
			$judge["exp"]	= "���㡼�����Ӿ���Υ���餬 {$Quantity}�Ͱʲ�";
			break;
//------------------------ ���㡼��+�Ӿ�(Ũ)
		case 1549:
			$judge["exp"]	= "���㡼��+�Ӿ�(Ũ)";
			$judge["css"]	= true;
			break;
		case 1550:// ���㡼�������꤬ {$Quantity}�Ͱʾ�
			$judge["exp"]	= "���㡼�������꤬ {$Quantity}�Ͱʾ�";
			break;
		case 1551:// ���㡼�������꤬ {$Quantity}�Ͱʲ�
			$judge["exp"]	= "���㡼�������꤬ {$Quantity}�Ͱʲ�";
			break;
		case 1555:// �Ӿ������꤬ {$Quantity}�Ͱʾ�
			$judge["exp"]	= "�Ӿ������꤬ {$Quantity}�Ͱʾ�";
			break;
		case 1556:// �Ӿ������꤬ {$Quantity}�Ͱʲ�
			$judge["exp"]	= "�Ӿ������꤬ {$Quantity}�Ͱʲ�";
			break;
		case 1560:// ���㡼�����Ӿ������꤬ {$Quantity}�Ͱʾ�
			$judge["exp"]	= "���㡼�����Ӿ������꤬ {$Quantity}�Ͱʾ�";
			break;
		case 1561:// ���㡼�����Ӿ������꤬ {$Quantity}�Ͱʲ�
			$judge["exp"]	= "���㡼�����Ӿ������꤬ {$Quantity}�Ͱʲ�";
			break;
//------------------------ ��
		case 1599:
			$judge["exp"]	= "��";
			$judge["css"]	= true;
			break;
		case 1600:// ��ʬ���Ǿ���
			$judge["exp"]	= "��ʬ�� �Ǿ���";
			break;
		case 1610:// �Ǿ��֤�̣���� {$Quantity}�Ͱʾ�
			$judge["exp"]	= "�Ǿ��֤�̣���� {$Quantity}�Ͱʾ�";
			break;
		case 1611:// �Ǿ��֤�̣���� {$Quantity}�Ͱʲ�
			$judge["exp"]	= "�Ǿ��֤�̣���� {$Quantity}�Ͱʲ�";
			break;
		case 1612:// �Ǿ��֤�̣���� {$Quantity}% �ʲ�
			$judge["exp"]	= "�Ǿ��֤�̣���� {$Quantity}% �ʾ�";
			break;
		case 1613:// �Ǿ��֤�̣���� {$Quantity}% �ʲ�
			$judge["exp"]	= "�Ǿ��֤�̣���� {$Quantity}% �ʲ�";
			break;
//------------------------ ��(Ũ)
		case 1614:
			$judge["exp"]	= "��(Ũ)";
			$judge["css"]	= true;
			break;
		case 1615:// �Ǿ��֤���꤬ {$Quantity}�Ͱʾ�
			$judge["exp"]	= "�Ǿ��֤���꤬ {$Quantity}�Ͱʾ�";
			break;
		case 1616:// �Ǿ��֤���꤬ {$Quantity}�Ͱʲ�
			$judge["exp"]	= "�Ǿ��֤���꤬ {$Quantity}�Ͱʲ�";
			break;
		case 1617:// �Ǿ��֤���꤬ {$Quantity}% �ʲ�
			$judge["exp"]	= "�Ǿ��֤���꤬ {$Quantity}% �ʾ�";
			break;
		case 1618:// �Ǿ��֤���꤬ {$Quantity}% �ʲ�
			$judge["exp"]	= "�Ǿ��֤���꤬ {$Quantity}% �ʲ�";
			break;
//------------------------ ����
		case 1699:
			$judge["exp"]	= "����";
			$judge["css"]	= true;
			break;
		case 1700:// ��ʬ������
			$judge["exp"]	= "��ʬ�� ����";
			break;
		case 1701:// ��ʬ������
			$judge["exp"]	= "��ʬ�� ����";
			break;

		case 1710:// ̣���� ����{$Quantity}�Ͱʾ�
			$judge["exp"]	= "̣���� ����{$Quantity}�Ͱʾ�";
			break;
		case 1711:// ̣���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣���� ����{$Quantity}�Ͱʲ�";
			break;
		case 1712:// ̣���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣���� ����{$Quantity}��";
			break;

		case 1715:// ̣���� ����{$Quantity}�Ͱʾ�
			$judge["exp"]	= "̣���� ����{$Quantity}�Ͱʾ�";
			break;
		case 1716:// ̣���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣���� ����{$Quantity}�Ͱʲ�";
			break;
		case 1717:// ̣���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "̣���� ����{$Quantity}��";
			break;
//------------------------ ����(Ũ)
		case 1749:
			$judge["exp"]	= "����(Ũ)";
			$judge["css"]	= true;
			break;
		case 1750:// ���� ����{$Quantity}�Ͱʾ�
			$judge["exp"]	= "���� ����{$Quantity}�Ͱʾ�";
			break;
		case 1751:// ���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "���� ����{$Quantity}�Ͱʲ�";
			break;
		case 1752:// ���� ����{$Quantity}��
			$judge["exp"]	= "���� ����{$Quantity}��";
			break;

		case 1755:// ���� ����{$Quantity}�Ͱʾ�
			$judge["exp"]	= "���� ����{$Quantity}�Ͱʾ�";
			break;
		case 1756:// ���� ����{$Quantity}�Ͱʲ�
			$judge["exp"]	= "���� ����{$Quantity}�Ͱʲ�";
			break;
		case 1757:// ���� ����{$Quantity}��
			$judge["exp"]	= "���� ����{$Quantity}��";
			break;
//------------------------ ����
		case 1799:
			$judge["exp"]	= "����";
			$judge["css"]	= true;
			break;
		case 1800:// ̣���� ��������餬 {$Quantity}ɤ�ʾ�
			$judge["exp"]	= "̣���� ��������餬 {$Quantity}ɤ�ʾ�";
			break;
		case 1801:// ̣���� ��������餬 {$Quantity}ɤ�ʲ�
			$judge["exp"]	= "̣���� ��������餬 {$Quantity}ɤ�ʲ�";
			break;
		case 1805:// ̣���� ��������餬 {$Quantity}ɤ
			$judge["exp"]	= "̣���� ��������餬 {$Quantity}ɤ";
			break;
//------------------------ ����(Ũ)
		case 1819:
			$judge["exp"]	= "����(Ũ)";
			$judge["css"]	= true;
			break;
		case 1820:// ���� ��������餬 {$Quantity}ɤ�ʾ�
			$judge["exp"]	= "���� ��������餬 {$Quantity}ɤ�ʾ�";
			break;
		case 1821:// ���� ��������餬 {$Quantity}ɤ�ʲ�
			$judge["exp"]	= "���� ��������餬 {$Quantity}ɤ�ʲ�";
			break;
		case 1825:// ���� ��������餬 {$Quantity}ɤ
			$judge["exp"]	= "���� ��������餬 {$Quantity}ɤ";
			break;

//------------------------ ��ˡ��
		case 1839:
			$judge["exp"]	= "��ˡ��";
			$judge["css"]	= true;
			break;
		case 1840:// ̣������ˡ�ؤο��� {$Quantity}�İʾ�
			$judge["exp"]	= "̣������ˡ�ؤο��� {$Quantity}�İʾ�";
			break;
		case 1841:// ̣������ˡ�ؤο��� {$Quantity}�İʲ�
			$judge["exp"]	= "̣������ˡ�ؤο��� {$Quantity}�İʲ�";
			break;
		case 1845:// ̣������ˡ�ؤο��� {$Quantity}��
			$judge["exp"]	= "̣������ˡ�ؤο��� {$Quantity}��";
			break;
//------------------------ ��ˡ��(Ũ)
		case 1849:
			$judge["exp"]	= "��ˡ��(Ũ)";
			$judge["css"]	= true;
			break;
		case 1850:// ������ˡ�ؤο��� {$Quantity}�İʾ�
			$judge["exp"]	= "������ˡ�ؤο��� {$Quantity}�İʾ�";
			break;
		case 1851:// ������ˡ�ؤο��� {$Quantity}�İʲ�
			$judge["exp"]	= "������ˡ�ؤο��� {$Quantity}�İʲ�";
			break;
		case 1855:// ������ˡ�ؤο��� {$Quantity}��
			$judge["exp"]	= "������ˡ�ؤο��� {$Quantity}��";
			break;

//------------------------ �����ư���
		case 1899:
			$judge["exp"]	= "�����ư���";
			$judge["css"]	= true;
			break;
		case 1900:// ��ʬ�ι�ư����� {$Quantity}��ʾ�
			$judge["exp"]	= "��ʬ�ι�ư�� {$Quantity}��ʾ�";
			break;
		case 1901:// ��ʬ�ι�ư����� {$Quantity}��ʲ�
			$judge["exp"]	= "��ʬ�ι�ư�� {$Quantity}��ʲ�";
			break;
		case 1902:// ��ʬ�ι�ư����� {$Quantity}����
			$judge["exp"]	= "��ʬ�� {$Quantity}���ܤι�ư";
			break;
//------------------------ �������
		case 1919:
			$judge["exp"]	= "�������";
			$judge["css"]	= true;
			break;
		case 1920:// {$Quantity}�����ɬ��
			$judge["exp"]	= "{$Quantity}����� ɬ��";
			break;
//------------------------ ��Ψ
		case 1939:
			$judge["exp"]	= "��Ψ";
			$judge["css"]	= true;
			break;
		case 1940:// {$Quantity}%�γ�Ψ��
			$judge["exp"]	= "{$Quantity}%�� ��Ψ��";
			break;


//----------------------- �ü�
		case 9000:// ���������Lv**�ʾ夬��롣
			$judge["exp"]	= "��������� Lv{$Quantity}�ʾ夬���";
			break;




		default:
$judge	= false;
	}

	return $judge;
}
?>