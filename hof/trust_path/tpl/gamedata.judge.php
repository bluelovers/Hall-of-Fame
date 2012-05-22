
	<?php $this->extend('gamedata/layout') ?>

	<div style="margin:0 15px">
		<h4>判定(judge)</h4>

		<table border="0" cellspacing="0" width="60%">
			<tbody>

				<?php foreach($this->output->list as $tagid => $list): ?>

					<tr>
						<td class="td6"><?php e($list['tag']['no']) ?></td>
						<td class="td6"><?php e($list['tag']['exp']) ?></td>
					</tr>

					<?php foreach($list['list'] as $no => $data): ?>

						<tr>
							<td class="td9"><?php e($data['no']) ?></td>
							<td class="td9"><?php e($data['exp']) ?></td>
						</tr>

					<?php endforeach; ?>

				<?php endforeach; ?>

			</tbody>
		</table>
	</div>

	<?php /*include (GAME_DATA_JUDGE);*/ ?>