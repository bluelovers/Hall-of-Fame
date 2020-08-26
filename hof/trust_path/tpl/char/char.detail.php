
	<form action="<?php e($this->output->char_url) ?>" method="post">
		<h4>Character Status<a href="<?php e(HOF::url('manual', 'manual', '#charstat')) ?>" target="_blank" class="a0">?</a></h4>
		<?php $this->output->char->ShowCharDetail(); ?>
		<?php if ($this->output->user_item["7500"]):?>
		<!-- 改名 -->
		<input type="submit" class="btn" name="rename" value="ChangeName">
		<?php endif; ?>
		<?php if ($this->output->user_item["7510"] || $this->output->user_item["7511"] || $this->output->user_item["7512"] || $this->output->user_item["7513"] || $this->output->user_item["7520"]): ?>
		<!-- ステータスリセット系 -->
		<input type="submit" class="btn" name="showreset" value="Reset">
		<?php endif; ?>
		<input type="submit" class="btn" name="byebye" value="Kick">
	</form>
