
	<div style="margin:15px">
		<h4>コロシアム(Colosseum)</h4>

		<?php foreach((array)$this->output->msg_result as $e): ?>
			<?php HOF_Helper_Global::HOF_Helper_Global::ShowResult($e[0], $e[1]); ?>
		<?php endforeach; ?>

		<?php foreach((array)$this->output->error as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

	</div>


