
	<div  style="padding:15px">
		|
		<?php foreach ($this->output->char_list as $k => $v): ?>
			<a href="<?php e(HOF::url('char', 'char', array('char' => $k))) ?>"><?php e($v) ?></a> |
		<?php endforeach; ?>
	</div>