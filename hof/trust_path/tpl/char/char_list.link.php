
	<div>
		CharList |
		<?php foreach ($this->output->char_list as $k => $v): ?>
			<a href="<?php e(HOF::url('char', $this->action, array('char' => $k))) ?>" <?php if ($k == $this->output->char_id) e(' class="a0"'); ?>><?php e($v) ?></a> |
		<?php endforeach; ?>
	</div>