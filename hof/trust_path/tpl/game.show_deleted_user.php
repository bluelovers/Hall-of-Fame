
	<?php $this->set('npc.talk.title', '墓地') ?>
	<?php $this->set('npc.talk.no', 'mon_034') ?>
	<?php ob_start(); ?>
		<p class="recover">Here only show deleted user Team ID.</p>
		<p class="recover">If you are Team ID owner, You can ask the SYSTEM Recovery.</p>

		<?php foreach((array)$this->output->error as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

		<?php foreach((array)$this->output->msg_result as $e): ?>
			<?php HOF_Helper_Global::ShowResult($e[0], $e[1]); ?>
		<?php endforeach; ?>

	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

	<div class="margin15">
		<h4>Deleted Team</h4>

		<?php if ($this->output->list_deleted_name): ?>

			<form action="<?php e(INDEX) ?>?show_deleted_user" method="post" style="padding-left:20px" autocomplete="off">
				<table>
					<tbody>
						<tr>
							<td><div style="text-align:right">
									Deleted User ID:
								</div></td>
							<td><input type="text" maxlength="16" class="text" name="deleted_id" style="width:160px" value="<?php e($this->output->deleted_id) ?>" autocomplete="off"></td>
						</tr>
						<tr>
							<td><div style="text-align:right">
									PASS:
								</div></td>
							<td><input type="password" maxlength="16" class="text" name="deleted_pass" style="width:160px"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" class="btn" name="show_deleted_user" value="Recovery" style="width:80px"> &nbsp;<a href="<?php e(HOF::url()) ?>">Login?</a></td>
						</tr>
					</tbody>
				</table>

				<p class="recover">Please choose your Team ID, the Team ID need match User ID</p>

				<ul>
				<?php foreach ((array)$this->output->list_deleted_name as $id => $name): ?>
					<?php if (empty($name)) continue ; ?>
					<li class="dmg" style="display: inline-block; width: auto; margin-right: 1em;"><label><input type="radio" name="deleted_team" value="<?php e($name) ?>" /> <?php e($name) ?></label></li>
				<?php endforeach; ?>
				</ul>

			</form>

		<?php else: ?>
			<p>No Deleted Team can Recover, or you can contact SYSTEM.</p>
		<?php endif; ?>
	</div>