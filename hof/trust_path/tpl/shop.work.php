
	<?php $this->extend('shop/layout'); ?>

	<?php $this->callMethod('_work'); ?>

	<div style="margin:15px">
		<h4>アルバイトする！</h4>
		<form method="post" action="<?php e(BASE_URL) ?>?menu=work">
			<p>1回 100Time<br />
				給与 :
				<?php e(HOF_Helper_Global::MoneyFormat($this->output->work_each_pay)) ?>/回</p>
			<select name="amount">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select>
			<br />
			<input type="submit" value="Work" class="btn" />
		</form>
	</div>