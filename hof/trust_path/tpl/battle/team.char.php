
	<div style="margin:15px">
		<h4>Teams</h4>
	</div>

	<form action="<?php e($this->get('battle.target.from.action', INDEX . '?common=' . $this->get('battle.target.id'))) ?>" method="post">

		<?php e(HOF_Class_Char_View::ShowCharacters(HOF_Model_Main::getInstance()->char, "CHECKBOX", HOF_Model_Main::getInstance()->party_memo)); ?>

		<div style="margin:15px;text-align:center;overflow: visible;">
			<input type="submit" class="btn" name="monster_battle" value="Battle !">
			<input type="reset" class="btn" value="Reset">
			<br>
			Save this party:
			<input type="checkbox" name="memory_party" value="1">
		</div>

	</form>