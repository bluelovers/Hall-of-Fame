	<div style="margin:15px">
		<p>
			<a href="?">Back</a>
			<br/>
			<a href="#btm">to bottom</a>
		</p>
		<form action="?update" method="post">
			<?php if ($this->output->updatepass): ?>
			<textarea class="text" rows="12" cols="60" name="updatetext"><?php e($this->output->update) ?></textarea>
			<br/>
			<input type="submit" class="btn" value="update">
			<a href="?update">リロード</a>
			<br/>
			<?php endif; ?>
			<?php e(nl2br($this->output->update)) ?>
			<br/>
			<a name="btm"></a>
			<input type="password" class="text" name="updatepass" style="width:100px" value="$_POST[updatepass]">
			<input type="submit" class="btn" value="update">
		</form>
		<p>
			<a href="?">Back</a>
		</p>
	</div>
