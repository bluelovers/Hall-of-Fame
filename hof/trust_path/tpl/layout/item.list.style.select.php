
		<form id="<?php e($this->output->id) ?>">
			<select name="<?php e($this->output->name) ?>" data-target="<?php e($this->output->target) ?>" data-item="type" style="margin: 1em">
				<option value="">-</option>
				<?php foreach($this->output->select as $k => $v): ?>
					<option value="<?php e($k) ?>" <?php e($k == $this->output->list_type ? 'selected="selected"' : '') ?>><?php e($v) ?></option>
				<?php endforeach; ?>
			</select>
		</form>
