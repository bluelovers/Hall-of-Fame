<?php if (empty($this->output->article_list)): ?>
<?php HOF_Helper_Global::ShowResult('競売物無し(no auction)') ?>
<?php else: ?>

	総出品数: <?php e($this->output->article_count) ?>

	<table style="width:100%;text-align:center" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=no')) ?>" <?php e($this->output->style_no) ?>>番号</a></td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=time')) ?>"<?php e($this->output->style_time) ?>>残り</a></td>
			<td class="td7 td6">
				<a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=price')) ?>"<?php e($this->output->style_price) ?>>価格</a>
				&nbsp;<a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=rprice')) ?>"<?php e($this->output->style_rprice) ?>>(昇)</a>
			</td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=item')) ?>"<?php e($this->output->style_item) ?>>Item</a></td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=bid')) ?>"<?php e($this->output->style_bid) ?>>入札数</a></td>
			<td class="td7 td6">入札者</td>
			<td class="td7 td6">出品者</td>
		</tr>

		<?php foreach ($this->output->article_list as $article_list): ?>
		<tr>
			<td class="td7" rowspan="2">
				<!-- 競売番号 -->
				<?php e($article_list["no"]) ?>
			</td>

			<td class="td7 nowrap">
				<!-- 終了時刻 -->
				<?php e($article_list['end_left']) ?>
			</td>

			<td class="td7 nowrap">
				<!-- 現在入札価格 -->
				<?php e(HOF_Helper_Global::MoneyFormat($article_list["price"])) ?>
			</td>

			<td class="td7">
				<!-- アイテム -->
				<?php e($article_list['item_show']) ?>
			</td>

			<td class="td7">
				<!-- 合計入札数 -->
				<?php e($article_list["TotalBid"]) ?>
			</td>

			<td class="td7">
				<!-- 入札者 -->
				<?php e($article_list['bidder_name']) ?>
			</td>

			<td class="td8">
				<!-- 出品者 -->
				<?php e($article_list['exhibitor_name']) ?>
			</td>

		</tr>
		<tr>
			<td colspan="6" style="text-align:left;" class="td8">

				<!-- 入札フォーム -->
				<?php if ($this->output->bidding): ?>
				<form action="<?php e(HOF::url($this->output->query)) ?>" method="post">
					<a href="javascript:void(0)" onclick="$('#Bid<?php e($article_list["no"]) ?>').toggle()" style="margin:0 10px">入札</a>

					<span style="display:none" id="Bid<?php e($article_list["no"]) ?>"> &nbsp;
						<input type="text" name="BidPrice" style="width:80px" class="text" value="<?php e($article_list['price_bid_min']) ?>">
						<input type="submit" value="Bid" class="btn">
						<input type="hidden" name="article_no" value="<?php e($article_list["no"]) ?>">
					</span>
				</form>
				<?php endif; ?>

				<?php e($article_list["comment"]) ?>
			</td>
		</tr>
		<?php endforeach; ?>

		<tr>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=no')) ?>" <?php e($this->output->style_no) ?>>番号</a></td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=time')) ?>"<?php e($this->output->style_time) ?>>残り</a></td>
			<td class="td7 td6">
				<a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=price')) ?>"<?php e($this->output->style_price) ?>>価格</a>
				&nbsp;<a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=rprice')) ?>"<?php e($this->output->style_rprice) ?>>(昇)</a>
			</td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=item')) ?>"<?php e($this->output->style_item) ?>>Item</a></td>
			<td class="td7 td6"><a href="<?php e(HOF::url($this->output->query[0], $this->output->query[1], $this->output->query[2].'&sort=bid')) ?>"<?php e($this->output->style_bid) ?>>入札数</a></td>
			<td class="td7 td6">入札者</td>
			<td class="td7 td6">出品者</td>
		</tr>

	</table>
	<?php endif; ?>
