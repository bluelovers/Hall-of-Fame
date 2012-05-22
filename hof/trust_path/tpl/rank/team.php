
	<div style="margin:15px">
		<h4>チーム設定(Team Setting)</h4>
		<p>
			ランキング戦用のチーム設定。
			<br />
			ここで設定したチームで戦います。
		</p>
	</div>

	<form action="<?php e(BASE_URL) ?>?menu=rank" method="post">

		<?php e(HOF_Class_Char_View::ShowCharacters(HOF::user()->char, INPUT_CHECKBOX, HOF::user()->party_rank)); ?>

		<div style="margin:15px;text-align:center;overflow: visible;">

			<?php if ($this->output->left_setting): ?>
				<div class="bold">
					<?php e($this->output->left_hour) ?>Hour <?php e($this->output->left_min) ?>minutes left to set again.
				</div>
			<?php endif; ?>

			<?php e($this->output->left_mes) ?>
			<input type="submit" class="btn" style="width:160px" value="SetTeam"<?php e($this->output->disable) ?> />
			<input type="hidden" name="SetRankTeam" value="1" />
			<p>
				設定後、
				<?php e($this->output->reset) ?>
				時間は変更できません。
				<br />
				Team setting disabled after
				<?php e($this->output->reset) ?>
				hours once set.
			</p>
		</div>
	</form>
