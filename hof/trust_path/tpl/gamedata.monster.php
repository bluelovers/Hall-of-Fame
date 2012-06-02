<?php $this->extend('gamedata/layout') ?>

	<div style="margin:0 15px">
		<h4>モンスター</h4>
		<table class="align-center" style="width:740px" cellspacing="0">
			<?php foreach($this->output->list as $data): ?>
			<tr>
				<td class="td6">Image</td>
				<td class="td6">EXP</td>
				<td class="td6">MONEY</td>
				<td class="td6">HP</td>
				<td class="td6">SP</td>
				<td class="td6">STR</td>
				<td class="td6">INT</td>
				<td class="td6">DEX</td>
				<td class="td6">SPD</td>
				<td class="td6">LUK</td>
			</tr>
			</td>

				<td class="td7"><?php $data['char']->ShowCharWithLand($data['char']->land); ?></td>
				<td class="td7"><?php e($data['char']->reward['exphold']) ?></td>
				<td class="td7"><?php e($data['char']->reward['moneyhold']) ?></td>
				<td class="td7"><?php e($data['char']->maxhp) ?></td>
				<td class="td7"><?php e($data['char']->maxsp) ?></td>
				<td class="td7"><?php e($data['monster']['str']) ?></td>
				<td class="td7"><?php e($data['monster']['int']) ?></td>
				<td class="td7"><?php e($data['monster']['dex']) ?></td>
				<td class="td7"><?php e($data['monster']['spd']) ?></td>
				<td class="td8"><?php e($data['monster']['luk']) ?></td>
			</tr>
			<tr>
				<td class="td7" colspan="11"><?php e($data['monster']['info']['desc']) ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
