
	<div style="margin:15px">
		<h4>GameData</h4>
		<div style="margin:0 20px">
			| <a href="<?php e(HOF::url('gamedata', 'job')) ?>">職(Job)</a> |
			<a href="<?php e(HOF::url('gamedata', 'item')) ?>">アイテム(item)</a> |
			<a href="<?php e(HOF::url('gamedata', 'judge')) ?>">判定</a> |
			<a href="<?php e(HOF::url('gamedata', 'monster')) ?>">モンスター</a> |
		</div>
	</div>

	<?php e($this->content) ?>
