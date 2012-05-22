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
		<form action="<?php e(HOF::url('recruit')) ?>" method="post" style="margin:15px">
			<h4>Sort of New Character</h4>
			<table cellspacing="0">
				<tbody>
					<?php foreach ($this->output->char_recruit as $list): ?>
						<tr>
							<?php foreach ($list as $i => $char): ?>
							<td class="td1" style="text-align:center; vertical-align: bottom;">
								<label>
									<?php $char->ShowImage() ?>
									<br/>
									<?php e(HOF_Helper_Global::MoneyFormat($char->recruit_money)); ?>
									<br/>
									<input type="radio" id="char_<?php e($i); ?>" name="recruit_no" value="<?php e($i); ?>" style="margin:3px"/>
								</label>
							</td>
							<?php endforeach; ?>
						</tr>
						<tr>
							<?php foreach ($list as $i => $char): ?>
							<td class="<?php e(($i % 2) ? 'td4' : 'td5') ?> td1" style="text-align:center">
								<label for="char_<?php e($i); ?>">
									<?php e($char->job_name)?>
								</label>
							</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<h4>New Character's Name &amp; Gender</h4>
			<table>
				<tbody>
					<tr>
						<td valign="top"><input type="text" class="text" name="recruit_name" style="width:160px" maxlength="16"/>
							<br>
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
