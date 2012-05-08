
	<?php $this->extend('log/layout'); ?>

	<div style="margin:15px">

		<h4>
			<?php if ($this->output->idx == 'ulog'): ?>
				ユニオン戦 - (Union Battle Log)
			<?php elseif ($this->output->idx == 'rlog'): ?>
				ランキング戦 - (Rank Battle Log)
			<?php else: ?>
				最近の戦闘 - (Recent Battles)
			<?php endif; ?>

			<?php if (!$this->output->full_log): ?>
				<a href="?<?php e($this->output->idx) ?>">全表示</a>
			<?php endif; ?>
		</h4>

	</div>

	<div style="padding:15px 0;width:100%;text-align:center" class="break">
		<h2>battle log*</h2>
		this battle starts at<br />
		<?php e(gc_date("m/d H:i:s", substr($this->output->time, 0, 10))) ?>
	</div>

	<?php $row = 6; ?>
	<?php while ($this->output->log[$row]): ?>
		<?php e($this->output->log["$row"]); ?>
		<?php $row++; ?>
	<?php endwhile; ?>
