
	<?php $this->extend('gamedata.layout') ?>

	<div style="margin:15px">
		<h4>職業(Job)</h4>
		<ul>
			<?php foreach($this->output->job_from as $job => $v): ?>
				<li>
					<a href="#<?php e($job) ?>"><?php e($this->output->list[$job]['job_name']) ?></a>
					<ul>
						<?php foreach((array)$v as $_k): ?>
							<li>
								<a href="#<?php e($_k) ?>"><?php e($this->output->list[$_k]['job_name']) ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
		<h4>Variety</h4>
		<table cellspacing="0" style="width:740px">
			<?php foreach($this->output->list as $no => $data): ?>
			<?php $flag = $flag ^ 1; ?>
			<?php $css = $flag ? ' class="td6"' : ' style="padding:3px;"'; ?>
			<tr>
				<td <?php e($css) ?> valign="top">
					<a name="<?php e($no) ?>"></a>
					<span class="bold">
						<?php e($data['gender'][1]['job_name']) ?>
						<?php if ($data['gender'][1]['job_name'] !== $data['gender'][2]['job_name']): ?>
							<br />
							(
								<?php e($data['gender'][2]['job_name']) ?>
							)
						<?php endif; ?>
					</span></td>
				<td <?php e($css) ?>>
					<img src="<?php e(HOF_Class_Icon::getImageUrl($data['gender'][1]['img'], HOF_Class_Icon::IMG_CHAR)) ?>" />
					<img src="<?php e(HOF_Class_Icon::getImageUrl($data['gender'][2]['img'], HOF_Class_Icon::IMG_CHAR)) ?>" />
				</td>
				<td <?php e($css) ?>>
					<?php e(nl2br($data['info']['desc'])) ?>
				</td>
			<tr>
				<td <?php e($css) ?> colspan="3">
					<div style="margin-left:30px">
						装備 :
						<?php e($data["equip"]) ?>
					</div>
				</td>
			</tr>
			<tr>
				<td <?php e($css) ?> colspan="3">
					<div style="padding-left:30px">
						<?php foreach($data['skill'] as $skill): ?>
							<?php HOF_Class_Skill::ShowSkillDetail($skill); ?>
							<br />
						<?php endforeach; ?>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
