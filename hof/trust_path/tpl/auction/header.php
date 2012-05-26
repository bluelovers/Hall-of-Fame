
<?php $this->set('npc.talk.title', 'オークション(Auction)') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'ori_003')) ?>
	<?php ob_start(); ?>
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

					<form action="<?php e(HOF::url('auction', $this->output->action)) ?>" method="post">
						<input type="submit" value="入会する" name="JoinMember" class="btn"/>
					</form>

				<?php endif; ?>

				<br /><br />

				<?php foreach((array)$this->output->msg_error as $e): ?>
					<?php HOF_Helper_Global::ShowError($e[0], $e[1]); ?>
				<?php endforeach; ?>

				<?php foreach((array)$this->output->msg_result as $e): ?>
					<?php HOF_Helper_Global::ShowResult($e[0], $e[1]); ?>
				<?php endforeach; ?>

	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

<div class="margin15">
	<h4>アイテム オークション(Item Auction) <a href="<?php e(HOF::url('auction')) ?>">更新</a></h4>
	<div class="margin15">
