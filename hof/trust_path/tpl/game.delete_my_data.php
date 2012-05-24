
	<div class="margin15">
		<h4>脱出口</h4>
		<div class="u">
			※データの削除
		</div>

		<?php foreach((array)$this->output->error['delete_my_data'] as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

		<form action="<?php e(HOF::url('game', 'delete_my_data')) ?>" method="post">
			PassWord :
			<input type="text" class="text" name="deletepass" size="20" autocomplete="off">
			<input type="submit" class="btn" name="delete" value="delete" style="width:100px">
		</form>
	</div>
