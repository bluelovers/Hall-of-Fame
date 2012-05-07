
	<?php foreach((array)$this->output->error as $e): ?>
		<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
	<?php endforeach; ?>
