
	<?php $this->extend('battle/layout') ?>

	<?php e($this->callMethod('_union')); ?>

	<?php if (!$this->output->monster_battle): ?>

		<?php e($this->slot('battle/team.char')); ?>

	<?php endif; ?>




