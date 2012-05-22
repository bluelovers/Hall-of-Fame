
	<div class="margin15">
		<h4><?php e($this->get('npc.talk.title')) ?></h4>
		<div>
			<div style="float:left; min-width:70px; margin-right: 1em; text-align: center;">
				<img src="<?php e(HOF_Class_Icon::getImageUrl($this->get('npc.talk.no'), HOF_Class_Icon::IMG_CHAR)); ?>" />
			</div>
			<div style="max-width:550px;display: inline-block;">
				<?php e($this->content) ?>
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
