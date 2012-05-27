
	<?php $this->extend('battle/hunt.layout') ?>

	<div style="margin:15px">
		<h4>CommonMonster</h4>
	</div>
	<div style="text-align: center;">
		<?php foreach ($this->output->maps as $map => $land): ?>
		<div class="land_frame">
			<div class="land" style="background-image : url(<?php e(HOF_Class_Icon::getImageUrl('land_'.$land['land']['land'], HOF_Class_Icon::IMG_LAND)) ?>);">
			</div>
			<span class="g_name"><a href="<?php e(HOF::url('battle', 'common', array('land' => $map))) ?>">
			<?php e($land['land']['name']) ?>
			</a></span> <span>(
			<?php e($land['land']['proper']) ?>
			)</span>
			<?php if (isset($land['_cache']['allow'])): ?>
			<span>- Allow:
			<?php e($land[_cache][allow]) ?>
			</span>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
