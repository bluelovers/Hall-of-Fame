<?php $this->extend('layout/layout.default') ?>


	<div id="main_frame">
		<div id="title">
			<img src="<?php e(HOF_Class_Icon::getImageUrl('title03', HOF_Class_Icon::IMG_IMAGE)); ?>">
		</div>

		<?php HOF_Model_Main::getInstance()->MyMenu(); ?>

		<div id="contents">

<?php e($this->content) ?>

