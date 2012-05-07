
	<?php $this->extend('battle/layout') ?>

	<?php e($this->callMethod('_simulate')); ?>

	<?php e($this->slot('battle/team.char')); ?>
