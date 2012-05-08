
	<?php $this->extend('battle/layout') ?>

	<?php if (!$this->output->monster_battle): ?>

		<?php e($this->slot('battle/team.char')); ?>

		<div style="margin:15px">
			<h4>MonsterAppearance</h4>
		</div>

		<?php e($this->output->monster_show); ?>

	<?php else: ?>

		<?php e($this->output->battle_show); ?>

	<?php endif; ?>




