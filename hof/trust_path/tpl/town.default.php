
	<div style="margin:15px">
		<h4>街</h4>
		<div class="town">
			<ul>
				<?php if ($this->output->list['Shop']): ?>

				<li>
					店(Shop)
					<ul>
						<li>
							<a href="?menu=buy">買う(Buy)</a>
						</li>
						<li>
							<a href="?menu=sell">売る(Sell)</a>
						</li>
						<li>
							<a href="?menu=work">アルバイト</a>
						</li>
					</ul>
				</li>

				<?php endif; ?>

				<?php if ($this->output->list['Recruit']): ?>

				<li>
					<p>
						<a href="?recruit">人材斡旋所(Recruit)</a>
					</p>
				</li>

				<?php endif; ?>

				<?php if ($this->output->list['Smithy']): ?>

				<li>
					鍛冶屋(Smithy)
					<ul>
						<li>
							<a href="?menu=refine">精錬工房(Refine)</a>
						</li>
						<li>
							<a href="?menu=create">製作工房(Create)</a>
						</li>
					</ul>
				</li>

				<?php endif; ?>

				<?php if ($this->output->list['Auction']): ?>

				<li>
					<a href="?menu=auction">オークション会場(Auction)
				</li>

				<?php endif; ?>

				<?php if ($this->output->list['colosseum']): ?>

				<li>
					<a href="?menu=rank">コロシアム(Colosseum)</a>
				</li>

				<?php endif; ?>
			</ul>
		</div>
		<h4>広場</h4>
		<form action="?town" method="post">
			<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
			<input type="submit" value="post" class="btn" style="width:100px" />
		</form>
		<?php foreach($this->output->log as $v): ?>
			<div>
				<?php e(nl2br($v)); ?>
			</div>
		<?php endforeach; ?>
	</div>
