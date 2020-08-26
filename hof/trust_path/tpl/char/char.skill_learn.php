
	<form action="<?php e(HOF::url('char', 'skill_learn', array('char' => $this->output->char_id))) ?>" method="post">

		<div class="u bold">Learn New</div>

		Skill Point : <?php e($this->output->char->skillpoint) ?>

		<ul class="g_list margin15">
		<?php foreach ($this->output->skill_learn as $skill): ?>
			<li><?php HOF_Class_Skill::ShowSkillDetail($skill, 1); ?></li>
		<?php endforeach; ?>
		</ul>

		<input type="submit" class="btn" name="skill_learn" value="Learn">
	</form>