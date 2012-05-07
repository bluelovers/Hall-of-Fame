	<?php $this->extend('recruit/layout'); ?>
	<?php $this->callMethod('_recruit'); ?>
	<?php if ($this->output->error_max): ?>

		<div style="margin:15px">
			<p>
				Maximum characters.
				<br>
				Need to make a space to recruit new character.
			</p>
			<p>
				キャラ数が限界に達しています。
				<br>
				新しいキャラを入れるには空きが必要です。
			</p>
		</div>
	<?php elseif ($this->output->char_recruit): ?>
		<form action="?recruit" method="post" style="margin:15px">
			<h4>Sort of New Character</h4>
			<table cellspacing="0">
				<tbody>
					<tr>
						<?php for ($i = 0; $i < 4; $i++): ?>
						<td class="td1" style="text-align:center"><?php $j = $i * 2; ?>
							<?php $this->output->char_recruit[$j]->ShowImage() ?>
							<?php $this->output->char_recruit[$j + 1]->ShowImage() ?>
							<br/>
							<input type="radio" name="recruit_no" value="<?php e($i + 1); ?>" style="margin:3px"/>
							<br/>
							<?php e(HOF_Helper_Global::MoneyFormat($this->output->char_recruit_money[$i+1])); ?>
							<?php endfor; ?>
					</tr>
					<tr>
						<?php for ($i = 0; $i < 4; $i++): ?>
						<?php $j = $i * 2; ?>
						<td class="<?php e(($i % 2) ? 'td4' : 'td5') ?>" style="text-align:center"><?php e($this->output->char_recruit[$j]->job_name)?></td>
						<?php endfor; ?>
					</tr>
				</tbody>
			</table>
			<h4>New Character's Name &amp; Gender</h4>
			<table>
				<tbody>
					<tr>
						<td valign="top"><input type="text" class="text" name="recruit_name" style="width:160px" maxlength="16"/>
							<br>
							<div style="margin:5px 0px">
								<input type="radio" class="vcent" name="recruit_gend" value="0"/>
								male
								<input type="radio" class="vcent" name="recruit_gend" value="1" style="margin-left:15px;"/>
								female
							</div>
							<input type="submit" class="btn" name="recruit" value="Recruit"/>
							<input type="hidden" class="btn" name="recruit" value="Recruit"/></td>
						<td valign="top"><p>
								1 to 16 letters.
								<br>
								Japanese characters count as 2.
								<br>
								日本語は1文字 = 2 letter.
							</p></td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php endif; ?>
