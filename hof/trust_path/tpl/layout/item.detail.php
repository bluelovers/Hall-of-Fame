
	<?php $item = $this->output['item']; ?>

	<div class="g_item" data-no="<?php e($item['no']) ?>">

		<span title="<?php if ($item['refine']):?>+<?php e($item['refine']) ?> <?php endif; ?><?php if ($item['AddName']):?><?php e($item['AddName']) ?> <?php endif; ?><?php e($item['base_name']) ?>">
			<img src="<?php e(HOF_Class_Icon::getImageUrl($item['img'], HOF_Class_Icon::IMG_ITEM))?>" class="vcent">

			<span class="g_name">
				<?php if ($item['refine']):?>
					+<?php e($item['refine']) ?>
				<?php endif; ?>


				<?php if ($item['AddName']):?>
					<span class="g_name_plus"><?php e($item['AddName']) ?></span>
				<?php endif; ?>

				<?php e($item['base_name']) ?>
			</span>
		</span>

		<?php if ($item['type']):?>
			<span class="light"> (<?php e($item['type']) ?>)</span>
		<?php endif; ?>

		<?php if ($this->output['amount']):?>
			x<span class="bold" style="font-size:80%"><?php e($this->output['amount']) ?></span>
		<?php endif; ?>

		<?php if ($item['atk'][0]):?>
			/ <span class="dmg">Atk:<?php e($item['atk'][0]) ?></span>
		<?php endif; ?>

		<?php if ($item['atk'][1]):?>
			/ <span class="spdmg">Matk:<?php e($item['atk'][1]) ?></span>
		<?php endif; ?>

		<?php if ($item['def']):?>
			/ <span class="recover">Def:<?php e($item['def'][0]) ?>+<?php e($item['def'][1]) ?></span>
			/ <span class="support">Mdef:<?php e($item['def'][2]) ?>+<?php e($item['def'][3]) ?></span>
		<?php endif; ?>

		<?php if ($item['P_SUMMON']):?>
			/ <span class="support">Summon+<?php e($item['P_SUMMON']) ?>%</span>
		<?php endif; ?>

		<?php if (isset($item['handle'])):?>
			/ <span class="charge">h:<?php e($item['handle']) ?></span>
		<?php endif; ?>

		<?php if ($item['option']):?>
			/ <span class="g_item_option" style="font-size:80%"><?php e(substr($item['option'], 0, -2)) ?></span>
		<?php endif; ?>

		<?php if ($this->output['need'] && $item['need']):?>
			/
			<?php foreach ($item['need'] as $M_itemNo => $M_amount): ?>

				<?php $M_item = HOF_Model_Data::getItemData($M_itemNo); ?>

				<img src="<?php e(HOF_Class_Icon::getImageUrl($M_item['img'], HOF_Class_Icon::IMG_ITEM))?>" class="vcent">

				<?php e($M_item['base_name']) ?>

				x<span class="bold" style="font-size:80%"><?php e($M_amount) ?></span>

				<?php if ($this->output['need'][$M_itemNo]):?>
					<span class="light">(<?php e($this->output['need'][$M_itemNo]) ?>)</span>
				<?php endif; ?>

			<?php endforeach; ?>
		<?php endif; ?>

	</div>
