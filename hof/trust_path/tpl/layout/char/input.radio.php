	<div class="carpet_frame">
		<div class="carpet<?php e($this->output->flag % 2) ?>">
			<a href="?char=<?php e($this->output->char->id) ?>">
			<?php $this->output->char->ShowImage(); ?>
			</a>
		</div>
		<div id="text<?php e($this->output->flag) ?>" <?php e($this->output->checked ? null : ' class="unselect"') ?>>
			<?php e($this->output->char->name) ?>
			<?php if ($this->output->char->statuspoint): ?>
			<span class="bold charge">*</span>
			<?php endif; ?>
			<br />
			Lv.<?php e($this->output->char->level) ?>
			&nbsp;
			<?php e($this->output->char->job_name()) ?>
		</div>
		<input type="<?php e($this->output->input_type) ?>" id="box<?php e($this->output->flag) ?>" name="input_char_id[]" value="<?php e($this->output->char->id) ?>" <?php e($this->output->checked ? 'checked="checked"' : '') ?> />
	</div>
