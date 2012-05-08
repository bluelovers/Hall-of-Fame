
	<?php $this->extend('log/layout'); ?>

	<div style="margin:15px">

		<!-- common -->

		<?php foreach($this->output->logs as $_k => $logs): ?>

			<h4>

				<?php if ($_k == LOG_BATTLE_UNION): ?>
					ユニオン戦 - (Union Battle Log)
					<?php $_u = 'ulog'; ?>
				<?php elseif ($_k == LOG_BATTLE_RANK): ?>
					ランキング戦 - (Rank Battle Log)
					<?php $_u = 'rlog'; ?>
				<?php else: ?>
					最近の戦闘 - (Recent Battles)
					<?php $_u = 'clog'; ?>
				<?php endif; ?>

				<?php if (!$this->output->full_log): ?>
					<a href="?<?php e($_u) ?>">全表示</a>
				<?php endif; ?>
			</h4>

			<?php foreach($logs as $log): ?>

				<?php list($time, $team, $number, $avelv, $win, $act, $date,) = $log; ?>

				[ <a href="?<?php e($_u) ?>=<?php e($time) ?>"><?php e($date) ?></a> ]

				<!-- 総ターン数 -->
				<span class="bold"><?php e($act) ?></span>turns

				<?php if ($win === "0"): ?>
					<span class="recover"><?php e($team[0]) ?></span>
				<?php elseif ($win === "1"): ?>
					<span class="dmg"><?php e($team[0]) ?></span>
				<?php else: ?>
					<?php e($team[0]) ?>
				<?php endif; ?>

				(<?php e($number[0]) ?>:<?php e($avelv[0]) ?>)
				vs
				<?php if ($win === "0"): ?>
					<span class="dmg"><?php e($team[1]) ?></span>
				<?php elseif ($win === "1"): ?>
					<span class="recover"><?php e($team[1]) ?></span>
				<?php else: ?>
					<?php e($team[1]) ?>
				<?php endif; ?>
				(<?php e($number[1]) ?>:<?php e($avelv[1]) ?>)

				<br />

			<?php endforeach; ?>

		<?php endforeach; ?>

	</div>