
	<?php $this->set('npc.talk.title', '墓地') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'ori_002')) ?>
	<?php ob_start(); ?>

		<?php foreach((array)$this->output->error as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

		<p class="result">Here only show deleted user Team ID.</p>
		<p class="result">If you are Team ID owner, You can ask the SYSTEM Recovery.</p>

	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

	<div class="margin15">
		<h4>Deleted Team</h4>

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
						<td><input type="submit" class="btn" name="show_deleted_user" value="Recovery" style="width:80px"></td>
					</tr>
				</tbody>
			</table>
		</form>

		<p>Please choose your Team ID, the Team ID need match User ID</p>

		<ul>
		<?php foreach ((array)$this->output->list_deleted_name as $id => $name): ?>
			<?php if (empty($name)) continue ; ?>
			<li class="dmg" style="display: inline-block; width: auto; margin-right: 1em;"><label><input type="radio" /> <?php e($name) ?></label></li>
		<?php endforeach; ?>
		</ul>
	</div>