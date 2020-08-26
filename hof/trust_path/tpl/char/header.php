


	<?php $this->set('npc.talk.title', 'Char') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'mon_267r')) ?>
	<?php ob_start(); ?>

		<?php e($this->slot('char/char_list.link')) ?>

		<?php e($this->slot('char/msg')) ?>

		<div>
			ActionList /
		<?php foreach(array('char', 'stup', 'action', 'equip', 'skill_learn', 'job_change') as $k): ?>

			<a href="<?php e(HOF::url('char', $k, array('char' => $this->output->char_id))) ?>" <?php if ($k == $this->action) e(' class="a0"'); ?>><?php e(HOF::putintoClassParts($k)) ?></a> /

		<?php endforeach; ?>
		</div>

	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

	<div class="margin15">
