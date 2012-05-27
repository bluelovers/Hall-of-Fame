
	<div>

		<?php foreach((array)$this->output->_msg_error as $v): ?>
			<?php HOF_Helper_Global::ShowError($v[0], $v[1]); ?>
		<?php endforeach; ?>

		<?php foreach((array)$this->output->_msg_result as $v): ?>
			<?php HOF_Helper_Global::ShowResult($v[0], $v[1]); ?>
		<?php endforeach; ?>

	</div>
