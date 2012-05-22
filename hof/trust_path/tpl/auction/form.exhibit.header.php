
		<div class="u bold">
			出品方法
		</div>

		<ol>
			<li>
				出品するアイテムを選択します。
			</li>
			<li>
				2個以上出品する場合、数量を入力します。
			</li>
			<li>
				出品している時間の長さを指定します。
			</li>
			<li>
				開始価格を指定します(記入無し = 0)
			</li>
			<li>
				コメントがあれば入力します。
			</li>
			<li>
				送信する。
			</li>
		</ol>

		<div class="u bold">
			注意事項
		</div>

		<ul>
			<li>
				出品には&nbsp;手数料として <span class="result"><?php e(HOF_Helper_Global::MoneyFormat($this->output->article_exhibit_cost)) ?></span> 必要です。
			</li>
			<li>
				ちゃんとうごいてくれなさそう
			</li>
		</ul>

		<a href="<?php e(HOF::url('auction')) ?>">一覧に戻る</a>
	</div>

	<h4>出品する</h4>

	<div style="margin-left:20px">

	<div class="u bold">
		出品可能な物一覧
	</div>