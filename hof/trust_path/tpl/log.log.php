
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
				<a href="<?php e(HOF::url($this->controller(), null, array('log' => $this->output->idx))) ?>">全表示</a>
			<?php endif; ?>
		</h4>

	</div>

	<?php if ($this->output->log): ?>

		<div style="padding:15px 0;width:100%;text-align:center" class="break">
			<h2>battle log*</h2>
			this battle starts at<br />
			<?php e(HOF_Helper_Global::gc_date("m/d(D) H:i:s", substr($this->output->log['time'], 0, 10))) ?>
		</div>

		<?php e($this->output->log['contents']); ?>

	<?php endif; ?>
