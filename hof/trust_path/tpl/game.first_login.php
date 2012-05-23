<form action="<?php e(HOF::url('game', 'first_login')) ?>" method="post" style="margin:15px">
		<h4>Name of Team</h4>

		<?php foreach((array)$this->output->error as $e): ?>
			<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
		<?php endforeach; ?>

	<p>Decide the Name of the team.<br />
		It should be more than 1 and less than 16 letters.<br />
		Japanese characters count as 2 letters.</p>
	<p>1-16文字でチームの名前決めてください。<br />
		日本語でもOK。<br />
		日本語は 1文字 = 2 letter</p>
	<div class="bold u">
		TeamName
	</div>
	<input class="text" style="width:160px" name="team_name" value="<?php e($this->output->team_name) ?>">
	<h4>First Character</h4>
	<p>Decide the name of Your First Charactor.<br>
		more than 1 and less than 16 letters.</p>
	<p>初期キャラの名前。</p>
	<div class="bold u">
		CharacterName
	</div>
	<input class="text" type="text" name="char_name" style="width:160px;margin-bottom:10px" value="<?php e($this->output->char_name) ?>">
	<table cellspacing="0">
		<tbody>
			<?php foreach ($this->output->char_recruit as $list): ?>
				<tr>
					<?php foreach ($list as $i => $char): ?>
					<td class="td1" style="text-align:center; vertical-align: bottom;">
						<label>
							<?php $char->ShowImage() ?>
							<br/>
							<input type="radio" id="char_<?php e($i); ?>" name="recruit_no" value="<?php e($i); ?>" <?php e($this->output->recruit_no == $i ? ' checked="checked"' : '') ?> style="margin:3px"/>
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
	<p>Choose your first character's job &amp; Gender.</p>
	<p>最初のキャラの職と性別</p>
	<input class="btn" style="width:160px" type="submit" value="Done" name="Done">
	<input type="hidden" value="1" name="Done">
	<input class="btn" style="width:160px" type="submit" value="logout" name="logout">
</form>