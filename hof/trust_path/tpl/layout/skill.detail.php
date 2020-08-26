<!-- skill -->

	<?php $skill = $this->output['skill']; ?>
	<?php $radio = $this->output['radio']; ?>

	<div class="g_skill" data-no="<?php e($skill["no"]) ?>">
		<?php if ($radio): ?>

		<label>
			<input type="radio" name="newskill" value="<?php e($skill["no"]) ?>" class="vcent" />

		<?php endif ;?>

			<span class="g_name" title="<?php e($skill["name"]) ?>">
				<img src="<?php e(HOF_Class_Icon::getImageUrl($skill["img"], HOF_Class_Icon::IMG_SKILL)) ?>" class="vcent" alt="<?php e($skill['name']) ?>">
				<?php e($skill["name"]) ?>
			</span>

		<?php if ($radio): ?>

		</label>

			/ <span class="bold"><?php e($skill["learn"]) ?></span>pt

		<?php endif ;?>

		<!-- 対象 -->
		<?php if ($skill["target"][0] == "all"): ?>
			/ <span class="charge"><?php e($skill["target"][0]) ?></span>
		<?php elseif ($skill["target"][0] == "enemy"): ?>
			/ <span class="dmg"><?php e($skill["target"][0]) ?></span>
		<?php elseif ($skill['target'][0] == "friend"): ?>
			/ <span class="recover"><?php e($skill["target"][0]) ?></span>
		<?php elseif ($skill['target'][0] == "self"): ?>
			/ <span class="support"><?php e($skill["target"][0]) ?></span>
		<?php elseif (isset($skill['target'][0])): ?>
			/ <?php e($skill['target'][0]) ?>
		<?php endif ;?>

		<!-- 単体or複数or全体 -->
		<?php if ($skill["target"][1] == "all"): ?>
			- <span class="charge"><?php e($skill["target"][1]) ?></span>
		<?php elseif ($skill['target'][1] == "individual"): ?>
			- <span class="recover"><?php e($skill["target"][1]) ?></span>
		<?php elseif ($skill['target'][1] == "multi"): ?>
			- <span class="spdmg"><?php e($skill["target"][1]) ?></span>
		<?php elseif (isset($skill["target"][1])): ?>
			- <?php e($skill["target"][1]) ?>
		<?php endif ;?>

		<?php if (isset($skill["sacrifice"])): ?>
			/ <span class="dmg">Sacrifice:<?php e($skill["sacrifice"]) ?>%</span>
		<?php endif; ?>

		<!-- 消費SP -->
		<?php if (isset($skill["sp"])): ?>
		/ <span class="support"><?php e($skill["sp"]) ?>sp</span>
		<?php endif ;?>

		<!-- 消費魔方陣 -->
		<?php if ($skill["MagicCircleDeleteTeam"]): ?>
		/ <span class="support">MagicCircle x<?php e($skill["MagicCircleDeleteTeam"]) ?></span>
		<?php endif ;?>

		<?php if ($skill["pow"]): ?>
		/ <span class="<?php ($skill["support"] ? "recover" : "dmg") ?>"><?php e($skill['pow']) ?>%</span>x<?php e($skill["target"][2] ? $skill["target"][2] : "1") ?>
		<?php endif ;?>

		<?php if ($skill["type"] == 1): ?>
		/ <span class="spdmg">Magic</span>
		<?php endif ;?>

		<?php if ($skill["quick"]): ?>
		/ <span class="charge">Quick</span>
		<?php endif ;?>

		<?php if ($skill["invalid"]): ?>
		/ <span class="charge">invalid</span>
		<?php endif ;?>

		<?php if ($skill["priority"] == "Back"): ?>
		/ <span class="support">BackAttack</span>
		<?php endif ;?>

		<?php if ($skill["CurePoison"]): ?>
		/ <span class="support">CurePoison</span>
		<?php endif ;?>

		<?php if ($skill["delay"]): ?>
		/ <span class="support">Delay-<?php e($skill['delay']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpMAXHP"]): ?>
			/ <span class="charge">MaxHP+<?php e($skill['UpMAXHP']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpMAXSP"]): ?>
			/ <span class="charge">MaxSP+<?php e($skill['UpMAXSP']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpSTR"]): ?>
			/ <span class="charge">Str+<?php e($skill['UpSTR']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpINT"]): ?>
			/ <span class="charge">Int+<?php e($skill['UpINT']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpDEX"]): ?>
			/ <span class="charge">Dex+<?php e($skill['UpDEX']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpSPD"]): ?>
			/ <span class="charge">Spd+<?php e($skill['UpSPD']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpLUK"]): ?>
			/ <span class="charge">Luk+<?php e($skill['UpLUK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpATK"]): ?>
			/ <span class="charge">Atk+<?php e($skill['UpATK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpMATK"]): ?>
			/ <span class="charge">Matk+<?php e($skill['UpMATK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpDEF"]): ?>
			/ <span class="charge">Def+<?php e($skill['UpDEF']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["UpMDEF"]): ?>
			/ <span class="charge">Mdef+<?php e($skill['UpMDEF']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownMAXHP"]): ?>
			/ <span class="dmg">MaxHP-<?php e($skill['DownMAXHP']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownMAXSP"]): ?>
			/ <span class="dmg">MaxSP-<?php e($skill['DownMAXSP']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownSTR"]): ?>
			/ <span class="dmg">Str-<?php e($skill['DownSTR']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownINT"]): ?>
			/ <span class="dmg">Int-<?php e($skill['DownINT']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownDEX"]): ?>
			/ <span class="dmg">Dex-<?php e($skill['DownDEX']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownSPD"]): ?>
			/ <span class="dmg">Spd-<?php e($skill['DownSPD']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownLUK"]): ?>
			/ <span class="dmg">Luk-<?php e($skill['DownLUK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownATK"]): ?>
			/ <span class="dmg">Atk-<?php e($skill['DownATK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownMATK"]): ?>
			/ <span class="dmg">Matk-<?php e($skill['DownMATK']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownDEF"]): ?>
			/ <span class="dmg">Def-<?php e($skill['DownDEF']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["DownMDEF"]): ?>
			/ <span class="dmg">Mdef-<?php e($skill['DownMDEF']) ?>%</span>
		<?php endif ;?>

		<?php if ($skill["PlusSTR"]): ?>
			/ <span class="charge">Str+<?php e($skill['PlusSTR']) ?></span>
		<?php endif ;?>

		<?php if ($skill["PlusINT"]): ?>
			/ <span class="charge">Int+<?php e($skill['PlusINT']) ?></span>
		<?php endif ;?>

		<?php if ($skill["PlusDEX"]): ?>
			/ <span class="charge">Dex+<?php e($skill['PlusDEX']) ?></span>
		<?php endif ;?>

		<?php if ($skill["PlusSPD"]): ?>
			/ <span class="charge">Spd+<?php e($skill['PlusSPD']) ?></span>
		<?php endif ;?>

		<?php if ($skill["PlusLUK"]): ?>
			/ <span class="charge">Luk+<?php e($skill['PlusLUK']) ?></span>
		<?php endif ;?>

		<?php if ($skill["charge"]["0"] || $skill["charge"]["1"]): ?>
			/ (<?php e($skill["charge"]["0"] ? $skill["charge"]["0"] : "0") ?>:<?php e($skill["charge"]["1"] ? $skill["charge"]["1"] : "0") ?>)
		<?php endif ;?>

		<!-- 武器制限表示 -->
		<?php if ($skill["limit"]): ?>
			/ Limit:<?php e(implode(', ', array_keys($skill["limit"]))) ?>
		<?php endif ;?>

		<?php if ($skill["exp"]): ?>
			/ <?php e($skill["exp"]) ?>
		<?php endif ;?>
	</div>
