
	<h4>Skill<a href="<?php e(HOF::url('manual', 'manual', '#skill')) ?>" target="_blank" class="a0">?</a></h4>

	<div class="u bold">Mastered</div>

	<ul class="g_list margin15">
	<?php foreach ($this->output->skill_list as $skill): ?>
		<li><?php HOF_Class_Skill::ShowSkillDetail($skill); ?></li>
	<?php endforeach; ?>
	</ul>
