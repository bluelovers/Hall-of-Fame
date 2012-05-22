

	<!-- 鍛冶屋 製作 ヘッダ -->

	<?php $this->set('npc.talk.title', '鍛冶屋(Smithy)<a name="sm"></a>') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'mon_053rz')) ?>
	<?php ob_start(); ?>
				ここでは&nbsp;アイテムの製作ができるぜ！<br />
				お前さんが持ってる素材から作れそうな装備を作れるぜ。<br />
				特別な素材を練り込めば特殊な武器も作れるぜ。<br />
				兄がやってる<span class="bold">精錬工房</span>は<a href="<?php e(HOF::url('smithy', 'refine')) ?>">コッチ</a>だ。<br />
				<a href="#mat">所持素材一覧</a>
	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

	<div style="margin:15px">
		<h4>アイテムの製作<a name="refine"></a></h4>
		<div style="margin:0 15px">
