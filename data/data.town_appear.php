<?php
// ���ł�����X�Ƃ��̏o�������Ƃ�...
// ���t�ʂł�����ꏊ��ς����Ƃ��A
// ����A�C�e�����Ȃ��ƍs���Ȃ��Ƃ��ł���
// �ʃt�@�C���ɂ���K�v���������̂��ǂ�������
function TownAppear($user) {
	$place	= array();

	// �������ōs����
	$place["Shop"]	= true;
	$place["Recruit"]	= true;
	$place["Smithy"]	= true;
	$place["Auction"]	= true;
	$place["Colosseum"]	= true;

	// ����̃A�C�e�����Ȃ��ƍs���Ȃ��{��
	//if($user->item[****])
	//	$place["****"]	= true;

	return $place;
}
?>