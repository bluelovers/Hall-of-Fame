
	<div class="margin15">
		<h4>チーム名の変更</h4>
		<form action="<?php e(HOF::url('game', 'setting_rename')) ?>" method="post">
			費用 :
			<?php e(HOF_Helper_Global::MoneyFormat(NEW_NAME_COST)) ?>
			<br />
			16文字まで(全角=2文字)
			<br />

			<?php foreach((array)$this->output->error['setting_rename'] as $e): ?>
				<?php HOF_Helper_Global::ShowError($e[0], 'margin15'); ?>
			<?php endforeach; ?>

			<?php foreach((array)$this->output->msg_result['setting_rename'] as $e): ?>
				<?php HOF_Helper_Global::ShowResult($e[0], 'margin15'); ?>
			<?php endforeach; ?>

			新しい名前 :
			<input type="text" class="text" name="NewName" size="20">
			<input type="submit" class="btn" value="change" style="width:100px" name="setting_rename">
		</form>
	</div>
