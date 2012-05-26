
	</div>

	<?php $this->extend('auction/layout'); ?>

	<a name="log"></a>

	<h4>オークションログ(AuctionLog)</h4>

	<div style="margin-left:20px">
		<?php e(implode('<br/>', array_reverse($this->controller->ItemAuction->log))); ?>

