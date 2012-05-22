
	<?php $this->extend('log/layout'); ?>

	<div style="margin:15px">

		<!-- common -->

		<?php if (empty($this->output->logs)): ?>
			log doesnt exists
		<?php else: ?>

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
						<a href="<?php e(HOF::url($this->controller(), null, array('log' => $_u))) ?>">全表示</a>
					<?php endif; ?>
				</h4>

				<?php if (empty($logs)): ?>
					log doesnt exists
				<?php else: ?>

					<?php foreach($logs as $log): ?>

						[ <a href="<?php e(HOF::url($this->controller(), 'log', array('log' => $_u, 'no' => $log['time']))) ?>"><?php e($log['date']) ?></a> ]

						<!-- 総ターン数 -->
						<span class="bold"><?php e($log['act']) ?></span>turns

						<?php if ($log['win'] === "0"): ?>
							<span class="recover"><?php e($log['team'][0]) ?></span>
						<?php elseif ($log['win'] === "1"): ?>
							<span class="dmg"><?php e($log['team'][0]) ?></span>
						<?php else: ?>
							<?php e($log['team'][0]) ?>
						<?php endif; ?>

						(<?php e($log['number'][0]) ?>:<?php e($log['avelv'][0]) ?>)
						vs
						<?php if ($log['win'] === "0"): ?>
							<span class="dmg"><?php e($log['team'][1]) ?></span>
						<?php elseif ($log['win'] === "1"): ?>
							<span class="recover"><?php e($log['team'][1]) ?></span>
						<?php else: ?>
							<?php e($log['team'][1]) ?>
						<?php endif; ?>
						(<?php e($log['number'][1]) ?>:<?php e($log['avelv'][1]) ?>)

						<br />

					<?php endforeach; ?>

				<?php endif; ?>

			<?php endforeach; ?>

		<?php endif; ?>

	</div>