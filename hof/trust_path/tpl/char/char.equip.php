
	<form action="<?php e(HOF::url('char', 'equip', array('char' => $this->output->char_id))) ?>" method="post">

		<div class="bold u">
			Current Equip's
		</div>

		<table class="margin15">
			<?php foreach ($this->output->equip as $slot => $item): ?>
				<tr>
					<td style="text-align:right"><?php e(HOF::putintoClassParts($slot, '-')) ?> :</td>
					<td><label><input type="radio" class="vcent" name="spot" value="<?php e($slot) ?>"/> <?php e($item->html()); ?></label></td>
				<tr>
			<?php endforeach; ?>
		</table>

		<input type="submit" class="btn" name="equip_remove" value="Remove"/>
		<input type="submit" class="btn" name="equip_remove_all" value="Remove All"/>

	</form>