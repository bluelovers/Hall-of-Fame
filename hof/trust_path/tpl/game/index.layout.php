
	<!-- ログイン -->

	<div style="width:730px;">
		<div style="width:350px;float:right">

			<?php e($this->content) ?>

		</div>
		<!-- 飾 -->
		<div style="width:350px;padding:15px;float:left;">
			<?php e($this->slot('game/index.info')); ?>
		</div>
		<div class="c-both">
		</div>
	</div>

	<?php e($this->slot('game/index.footer')); ?>
