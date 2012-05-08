
	<div style="margin:15px">
		<h4>CommonMonster</h4>

		<div style="margin:0 20px">
			<?php foreach ($this->output->maps as $map => $land): ?>
			<p>
				<a href="?common=<?php e($map) ?>"><?php e($land['land']['name']) ?></a> (
				<?php e($land['land']['proper']) ?>
				)
				<?php if (isset($land['_cache']['allow'])): ?>
				- Allow:
				<?php e($land[_cache][allow]) ?>
				<?php endif; ?>
			</p>
			<?php endforeach; ?>
		</div>

		<?php if ($this->output->union): ?>
			<h4>UnionMonster</h4>
			<?php if ($this->output->result !== true): ?>
				<div style="margin:0 20px">
					Time left to next battle : <span class="bold">
					<?php e($this->output->left_minute . ":" . sprintf("%02d", $this->output->left_second)) ?>
					</span>
				</div>
			<?php endif; ?>

			</div>

			<?php e($this->output->union_showchar) ?>

		<?php else: ?>
	</div>
	<?php endif; ?>

	<div style="margin:0 15px">
		<h4>Union Battle Log <a href="?ulog">全表示</a></h4>
		<div style="margin:0 20px">
			<?php foreach ($this->output->logs as $file): ?>
			<?php HOF_Helper_Global::BattleLogDetail($file, "UNION"); ?>
			<?php endforeach; ?>
		</div>
	</div>
