<?php $this->extend('char/layout') ?>

	<?php e($this->slot('char/char.detail')) ?>

	<?php e($this->slot('char/char.statuspoint')) ?>

	<?php e($this->slot('char/char.judge')) ?>

	<?php e($this->slot('char/char.position')) ?>

	<?php e($this->output->content) ?>
