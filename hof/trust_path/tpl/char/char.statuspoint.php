
	<?php if (0 < $this->output->char->statuspoint): ?>

	<!-- ステータス上昇 -->
	<form action="<?php e(BASE_URL) ?>?char=<?php e($this->output->char_id) ?>" method="post" style="padding:0 15px">
		<h4>Status <a href="<?php e(HOF::url('manual', 'manual', '#statup')) ?>" target="_blank" class="a0">?</a></h4>
		<?php $Stat = array(
				"Str",
				"Int",
				"Dex",
				"Spd",
				"Luk"); ?>
		Point :
		<?php e($this->output->char->statuspoint) ?>
		<br />
		<?php foreach ($Stat as $val): ?>
			<?php e($val) ?>:
			<select name="up<?php e($val) ?>" class="vcent">
				<?php for ($i = 0; $i < $this->output->char->statuspoint + 1; $i++) : ?>
					<option value="<?php e($i) ?>">+<?php e($i) ?></option>
				<?php endfor; ?>
			</select>
		<?php endforeach; ?>
		<br />
		<input type="submit" class="btn" name="stup" value="Increase Status">
	</form>

	<?php endif; ?>
