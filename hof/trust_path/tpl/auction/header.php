<div style="margin:15px 0 0 15px">
	<h4>オークション(Auction)</h4>
	<div style="margin-left:20px">
		<div style="width:500px">
			<div style="float:left;width:50px;">
				<img src="<?php e(HOF_Class_Icon::getImageUrl('ori_003', IMG_CHAR)); ?>" />
			</div>
			<div style="float:right;width:450px;">
				<?php if ($this->controller->AuctionEnter()): ?>

					お客様は会員証をお持ちですね。
					<br />
					ようこそオークション会場へ。
					<br />

					<a href="#log">記録の回覧</a>
				<?php else: ?>

					オークションへの出品・入札には入会が必要です。
					<br />
					入会費は&nbsp;
					<?php e(HOF_Helper_Global::MoneyFormat(round(START_MONEY * 1.10))); ?>
					&nbsp;です。
					<br />
					入会しますか?
					<br />

					<form action="" method="post">
						<input type="submit" value="入会する" name="JoinMember" class="btn"/>
					</form>

				<?php endif; ?>
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<h4>アイテム オークション(Item Auction)</h4>
	<div style="margin-left:20px">
