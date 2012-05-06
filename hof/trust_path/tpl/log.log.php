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
