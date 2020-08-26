	<?php $this->extend('char/layout') ?>

	<h4>Equipment<a href="<?php e(HOF::url('manual', 'manual', '#equip')) ?>" target="_blank" class="a0">?</a></h4>

	<?php e($this->slot('char/char.equip')) ?>

	<?php e($this->output->content) ?>
