
			<?php $this->extend('game/login.layout') ?>

			<h4 style="width:350px">Login</h4>

			<?php if ($this->output->message): ?>
				<div class="recover margin15"><?php e($this->output->message) ?></div>
			<?php endif; ?>

			<form action="<?php e(INDEX) ?>" method="post" style="padding-left:20px">
				<table>
					<tbody>
						<tr>
							<td><div style="text-align:right">
									ID:
								</div></td>
							<td><input type="text" maxlength="16" class="text" name="id" style="width:160px" value="<?php e($this->output->id) ?>"></td>
						</tr>
						<tr>
							<td><div style="text-align:right">
									PASS:
								</div></td>
							<td><input type="password" maxlength="16" class="text" name="pass" style="width:160px"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" class="btn" name="login" value="login" style="width:80px">
								&nbsp;<a href="?newgame">NewGame?</a></td>
						</tr>
					</tbody>
				</table>
			</form>