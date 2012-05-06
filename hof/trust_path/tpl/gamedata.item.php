
	<?php $this->extend('gamedata.layout') ?>

	<div style="margin:0 15px">

	<?php foreach($this->output->list as $type => $data): ?>

		<h4><?php e($type) ?></h4>

		<?php foreach($data as $v): ?>

			<?php HOF_Class_Item::ShowItemDetail($v); ?>

			<br />

		<?php endforeach; ?>

	<?php endforeach; ?>

	</div>