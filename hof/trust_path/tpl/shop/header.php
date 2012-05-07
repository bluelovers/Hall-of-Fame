
	<!-- 店ヘッダ -->

	<?php $this->set('npc.talk.title', '店') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'ori_002')) ?>
	<?php ob_start(); ?>
				いらっしゃいませー<br />
				<a href="?menu=buy">買う</a>/<a href="?menu=sell">売る</a><br />
				<a href="?menu=work">アルバイト</a>
	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>
