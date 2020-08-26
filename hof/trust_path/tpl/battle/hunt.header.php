
	<?php $this->set('npc.talk.title', 'Hunt') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'noimage')) ?>
	<?php ob_start(); ?>

		<a href="<?php e(HOF::url('battle', 'list_common')) ?>">CommonMonster</a> / <a href="<?php e(HOF::url('battle', 'list_union')) ?>">UnionMonster</a><br />

	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>
