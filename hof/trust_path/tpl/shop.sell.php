
	<?php $this->extend('shop/layout'); ?>

	<?php $this->callMethod('_sell'); ?>

	<div style="margin:15px">
		<h4>売る</h4>

		<form action="<?php e(HOF::url('shop', 'sell')) ?>" method="post">
			<table cellspacing="0" data-item="table">

				<thead>
					<tr>
						<td class="td6"></td>
						<td style="text-align:center" class="td6">売値</td>
						<td style="text-align:center" class="td6">数</td>
						<td style="text-align:center" class="td6">アイテム</td>
					</tr>
				</thead>

				<tbody>
				<?php foreach((array)$this->output->shoplist as $no => $item): ?>

					<tr>
						<td class="td7" data-no="<?php e($no) ?>"><input type="checkbox" name="check_<?php e($no) ?>" value="1" data-no="<?php e($no) ?>"></td>
						<td class="td7" data-no="<?php e($no) ?>"><?php e($item->price_sell(1)) ?></td>
						<td class="td7" data-no="<?php e($no) ?>"><input type="text" id="text_<?php e($no) ?>" name="amount_<?php e($no) ?>" value="<?php e($item->amount) ?>" style="width:60px" class="text"></td>
						<td class="td8" data-no="<?php e($no) ?>"><?php e($item->html($item->amount)) ?></td>
					</tr>
				</tbody>

				<?php endforeach; ?>
			</table>
			<input type="submit" name="ItemSell" value="Sell" class="btn">
		</form>
	</div>