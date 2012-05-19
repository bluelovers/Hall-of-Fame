
	<?php if ($this->output->chars): ?>

	<div class="margin15">
		<?php HOF_Class_Char_View::ShowCharacters($this->output->chars, INPUT_RADIO) ?>
	</div>

		<?php if (0): ?>

	<table cellspacing="0" style="width:100%">
		<tbody>
			<tr>
				<?php foreach ($this->output->chars as $val): ?>
				<?php if ($i % CHAR_ROW == 0 && $i != 0): ?>
			</tr>
			<tr>
				<?php endif; ?>
				<td valign="bottom" style="width:<?php e($this->output->width) ?>%"><?php $val->ShowCharLink(); ?></td>
				<?php $i++; ?>
				<?php endforeach; ?>
			</tr>
		</tbody>
	</table><?php endif; ?>
	<?php endif; ?>