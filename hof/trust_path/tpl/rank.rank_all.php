
	<?php $this->extend('rank/layout') ?>

	<div style="margin:15px">
		<h4>Ranking - <?php e(HOF_Helper_Global::gc_date("Y年n月j日 G時i分s秒")) ?></h4>

		<?php $this->callMethod('_ShowRanking', 1); ?>
	</div>
