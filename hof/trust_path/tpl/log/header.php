
	<div style="margin:15px">

		<a href="<?php e(HOF::url($this->controller())) ?>" <?php if ($this->output->idx == 'log') e(' class="a0"'); ?>>All</a>
		<a href="<?php e(HOF::url($this->controller(), null, 'log=clog')) ?>" <?php if ($this->output->idx == 'clog') e(' class="a0"'); ?>>Common</a>
		<a href="<?php e(HOF::url($this->controller(), null, 'log=ulog')) ?>" <?php if ($this->output->idx == 'ulog') e(' class="a0"'); ?>>Union</a>
		<a href="<?php e(HOF::url($this->controller(), null, 'log=rlog')) ?>" <?php if ($this->output->idx == 'rlog') e(' class="a0"'); ?>>Ranking</a>

	</div>

	<?php foreach((array)$this->output->error as $e): ?>
		<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
	<?php endforeach; ?>