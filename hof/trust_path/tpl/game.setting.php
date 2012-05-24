
	<div style="margin:15px">
		<h4>Setting</h4>
		<form action="<?php e(HOF::url('game', 'setting')) ?>" method="post">
			<table>
				<tbody>
					<tr>
						<td><input type="checkbox" name="record_battle_log" value="1" <?php e($this->output->record_btl_log) ?>></td>
						<td>戦闘ログの記録</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="no_JS_itemlist" value="1" <?php e($this->output->no_JS_itemlist) ?>></td>
						<td>アイテムリストにJavaScriptを使わない</td>
					</tr>
				</tbody>
			</table>
			<!--<tr><td>None</td><td><input type="checkbox" name="none" value="1"></td></tr>-->
			Color :
			<select name="color" class="bgcolor">
				<?php foreach ($this->output->colors as $value): ?>
				<option value="<?php e($value) ?>" style="color: #<?php e($value) ?>" <?php if ($this->output->UserColor == $value) e(" selected"); ?> > SampleColor</option>
				<?php endforeach; ?>
			</select>
			<br />
			<input type="submit" class="btn" name="setting01" value="modify" style="width:100px">
			<input type="hidden" name="setting01" value="1">
		</form>
		<h4>Logout</h4>
		<form action="<?php e(HOF::url('game', 'logout')) ?>" method="post">
			<input type="submit" class="btn" name="logout" value="logout" style="width:100px">
		</form>
	</div>

	<?php e($this->slot('game.setting_rename')) ?>

	<?php e($this->slot('game.delete_my_data')) ?>