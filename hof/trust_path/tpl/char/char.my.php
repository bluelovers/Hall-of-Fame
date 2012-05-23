
	<?php if ($this->output->chars): ?>

	<div class="margin15">
		<?php HOF_Class_Char_View::ShowCharacters($this->output->chars, INPUT_RADIO) ?>
	</div>

	<?php endif; ?>