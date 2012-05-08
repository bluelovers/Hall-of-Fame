
	<div style="margin:15px">
		<h4>とりあえず New Game!</h4>

		<?php foreach((array)$this->output->error as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

		<?php if (!$this->output->user_full): ?>
			<form action="<?php e(INDEX) ?>?newgame" method="post">
				<table>
					<tbody>
						<tr>
							<td colspan="2">ID & PASS must be 4 to 16 letters.
								<br />
								letters allowed a-z,A-Z,0-9
								<br />
								ID と PASSは 4-16 文字以内で。半角英数字。</td>
						</tr>
						<tr>
							<td><div style="text-align:right">
									ID:
								</div></td>
							<td><input type="text" maxlength="16" class="text" name="Newid" style="width:240px" value="<?php e($this->output->newid) ?>"></td>
						</tr>
						<tr>
							<td colspan="2"><br />
								Password,Re-enter.
								<br />
								PASS とその再入力です 確認用。</td>
						</tr>
						<tr>
							<td><div style="text-align:right">
									PASS:
								</div></td>
							<td><input type="password" maxlength="16" class="text" name="pass1" style="width:240px"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="password" maxlength="16" class="text" name="pass2" style="width:240px">
								(verify)</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" class="btn" name="Make" value="Make" style="width:160px"></td>
						</tr>
					</tbody>
				</table>
			</form>
		<?php endif; ?>
	</div>
