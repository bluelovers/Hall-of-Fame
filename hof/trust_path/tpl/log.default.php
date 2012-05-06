
	<div style="margin:15px">

		<a href="?log" class="a0">All</a>
		<a href="?clog">Common</a>
		<a href="?ulog">Union</a>
		<a href="?rlog">Ranking</a>

		<!-- common -->

		<?php foreach($this->output->logs as $_k => $logs): ?>

			<?php $_u = 'log'; ?>

			<?php if ($_k == LOG_BATTLE_NORMAL): ?>
				<h4>最近の戦闘 - <a href="?clog">全表示</a>(Recent Battles)</h4>
			<?php elseif ($_k == LOG_BATTLE_UNION): ?>
				<h4>ユニオン戦 - <a href="?ulog">全表示</a>(Union Battle Log)</h4>
				<?php $_u = 'ulog'; ?>
			<?php elseif ($_k == LOG_BATTLE_RANK): ?>
				<h4>ランキング戦 - <a href="?rlog">全表示</a>(Rank Battle Log)</h4>
				<?php $_u = 'rlog'; ?>
			<?php endif; ?>

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