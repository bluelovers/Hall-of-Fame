
	<div  style="padding:15px">
		|
		<?php foreach ($this->output->char_list as $k => $v): ?>
			<a href="?char=<?php e($k) ?>"><?php e($v) ?></a> |
		<?php endforeach; ?>
	</div>