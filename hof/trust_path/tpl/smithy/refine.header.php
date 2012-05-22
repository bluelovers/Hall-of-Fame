

	<!-- 鍛冶屋 精錬 ヘッダ -->

	<?php $this->set('npc.talk.title', '鍛冶屋(Smithy)<a name="sm"></a>') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'mon_053r')) ?>
	<?php ob_start(); ?>
				ここでは&nbsp;アイテムの精錬ができるぜ！<br />
				精錬する物と精錬回数を選んでくれ。<br />
				ただし壊れても責任は持てないぜ。<br />
				弟がやってる<span class="bold">製作工房</span>は<a href="<?php e(HOF::url('smithy', 'create')) ?>">アッチ</a>だ。
	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

	<div style="margin:15px">
		<h4>アイテムの精錬<a name="refine"></a></h4>
		<div style="margin:0 15px">
