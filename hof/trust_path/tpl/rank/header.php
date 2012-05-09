
	<?php $this->set('npc.talk.title', 'コロシアム(Colosseum)') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'ori_002')) ?>
	<?php ob_start(); ?>
				<?php foreach((array)$this->output->msg_result as $e): ?>
					<?php HOF_Helper_Global::ShowResult($e[0], $e[1]); ?>
				<?php endforeach; ?>

				<?php foreach((array)$this->output->error as $e): ?>
					<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
				<?php endforeach; ?>
	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>


