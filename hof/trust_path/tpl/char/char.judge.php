
	<form action="<?php e(HOF::url('char', null, array('char' => $this->output->char_id))) ?>" method="post" style="padding:0 15px">
		<h4>Action Pattern<a href="<?php e(HOF::url('manual', 'manual', '#jdg')) ?>" target="_blank" class="a0">?</a></h4>
		<table cellspacing="5">
			<tbody>
				<?php for ($i = 0; $i < $this->output->pattern_max; $i++): ?>
				<tr>
					<td>
						<!-- No -->
						<?php e($i + 1) ?>
					</td>
					<td>
						<!-- JudgeSelect(判定の種類) -->
						<select name="judge<?php e($i) ?>">
							<?php $_init = 0; ?>
							<?php $pattern = $this->output->char->pattern_item($i); ?>
							<!-- 判断のoption -->
							<?php foreach ($this->output->judge_list as $val): ?>
								<?php $exp = HOF_Model_Data::getJudgeData($val); ?>
								<?php if ($exp["css"]): ?>
									<optgroup class="select0" label="<?php e($exp['exp']) ?>"></optgroup>
								<?php else: ?>
									<option value="<?php e($val) ?>" <?php e($pattern['judge'] == $val ? ' selected="selected"' : '') ?> >&nbsp;&nbsp;&nbsp;
										<?php e($exp['exp']) ?>
									</option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<!-- 数値(量) -->
						<input type="text" name="quantity<?php e($i) ?>" maxlength="4" value="<?php e($pattern['quantity']) ?>" style="width:56px" class="text"></td>
					<td>
						<!-- SkillSelect(技の種類) -->
						<select name="skill<?php e($i) ?>">
							<!-- 技のoption -->
							<?php foreach ($this->output->char->skill as $val): ?>
								<?php $skill = HOF_Model_Data::getSkill($val); ?>
								<option value="<?php e($val) ?>" <?php e($pattern['action'] == $val ? ' selected="selected"' : '') ?> >
									<?php e($skill["name"]) ?>
									<?php if(isset($skill["sp"])): ?>
										- (SP:<?php e($skill["sp"]) ?>)
									<?php endif; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="radio" name="pattern_no" value="<?php e($i) ?>">
					</td>
				</tr>
				<?php endfor; ?>
			</tbody>
		</table>
		<input type="submit" class="btn" value="Set Pattern" name="pattern_change">
		<input type="submit" class="btn" value="Set & Test" name="TestBattle">
		&nbsp;<a href="<?php e(HOF::url('battle', 'simulate')) ?>">Simulate</a>
		<br />
		<input type="submit" class="btn" value="Switch Pattern" name="pattern_memo">
		<input type="submit" class="btn" value="Add" name="pattern_insert">
		<input type="submit" class="btn" value="Delete" name="pattern_remove">
	</form>
