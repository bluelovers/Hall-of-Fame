<?php $this->extend('rank/layout') ?>

	<?php if ($this->output->show): ?>

	<div style="margin:15px">
		<form action="?menu=rank" method="post">
			<h4>ランキング(Ranking) -<a href="?rank">全ランキングを見る</a>&nbsp;<a href="?manual#ranking" target="_blank" class="a0">?</a></h4>

			<div style="width:100%;padding-left:30px">
				<div style="float:left;width:50%">
					<div class="u">
						TOP 5
					</div>

					<?php $this->callMethod('_ShowRanking'); ?>

				</div>
				<div style="float:right;width:50%">
					<div class="u">
						NEAR 5
					</div>

					<?php $this->callMethod('_ShowRankingRange'); ?>

				</div>
				<div style="clear:both">
				</div>
			</div>

			<?php if ($this->output->CanRankBattle_time): ?>
			<div class="recover margin15">
				Time left to Next :
				<span class="bold">
					<?php e($this->output->CanRankBattle_time) ?>
				</span>
			</div>
			<?php endif; ?>

			<input type="submit" class="btn" value="challenge!" name="ChallengeRank" style="width:160px" <?php e($this->output->disableRB) ?> />
		</form>
		<?php e($this->slot('rank/team')); ?>
	</div>

	<?php endif; ?>
