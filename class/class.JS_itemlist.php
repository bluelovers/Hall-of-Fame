<?php
// Javascript�Ǽ�����ڤ��ؤ���ɽ����

// ����ץ�
/*
$list0	= new JS_ItemList();
$list0->SetID("buy");//�����ʤ�Ǥ⤤��
$list0->SetName("type_buy");//�����ʤ�Ǥ⤤��
foreach($ShopList as $no) {
	$item	= LoadItemData($no);
	$head	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">'.MoneyFormat($item["buy"]);
	$head	.= ShowItemDetail($item,false,1);
	$list0->AddItem($item,$head);
}
	print($list0->GetJavaScript("list"));
	print($list0->ShowSelect());
------------------ ����ɬ��
<form action="?" method="post">
<div id="list"><?=$list0->ShowDefault()?></div>
<input type="submit" class="btn" name="shop_buy" value="Buy">
</form>
-----------------------
����ջ���
<form></form>
������Ҥˤʤ�ʤ��褦����ա�

print($list0->ShowSelect());
��<form>��������</form>�ǽ񤭽Ф���롣
print("<form>");
print($list0->ShowSelect());
print("</form>");// �η��ˤʤ��ư���ʤ���
*/

/*
	// �ơ��֥������ɽ����������
	// �ƥ����ƥ�ι��ܤ�\ n�ϻȤ��ʤ�������
$list0->ListTable("<table>");// �ơ��֥륿���ΤϤ��ޤ�
$list0->ListTableInsert("<tr><td>No</td><td>Item</td></tr>"); // �ơ��֥�κǽ�ȺǸ�ιԤ�ɽ���������ġ�
*/

class JS_ItemList {
	var $ID;
	var $name;

	var $weapon	= array();
	var $armor	= array();
	var $item	= array();
	var $other	= array();

	//
	var $Table	= false;
	var $TableInsert;

	// JS����Ѥ��ʤ�������
	var $NoJS;

	function SetID($ID) {
		$this->ID	= $ID;
	}

	function SetName($name) {
		$this->name	= $name;
	}

	// JS����Ѥ��ʤ�������
	function NoJS() {
		$this->NoJS	= true;
	}

	// �����ƥ���ɲ�
	function AddItem($item,$string) {
		switch($item["type"]) {
			case "Sword":
			case "TwoHandSword":
			case "Dagger":
			case "Wand":
			case "Staff":
			case "Bow":
			case "Whip":
				array_push($this->weapon,$string);
				break;
			case "Shield":
			case "Book":
			case "Armor":
			case "Cloth":
			case "Robe":
				array_push($this->armor,$string);
				break;
			case "Item":
				array_push($this->item,$string);
				break;
			default:
				array_push($this->other,$string);
		}
	}

	// �ơ��֥륿����ɽ������ɽ������褦�ˤ��롣
	function ListTable($HTML) {
		$this->Table	= $HTML;
	}

	// �ơ��֥�ΰ��־�Ȳ���ɬ��ɽ��������ܤߤ����ʤ�
	function ListTableInsert($string) {
		$this->TableInsert	= $string;
	}

	// JS���֤���
	function GetJavaScript($Id) {
		if($this->NoJS)
			return NULL;

		foreach ($this->weapon as $str)
			$JS_weapon	.= ($JS_weapon?" + \n'":"'").$str."'";
		foreach ($this->armor as $str)
			$JS_armor	.= ($JS_armor?" + \n'":"'").$str."'";
		foreach ($this->item as $str)
			$JS_item	.= ($JS_item?" + \n'":"'").$str."'";
		foreach ($this->other as $str)
			$JS_other	.= ($JS_other?" + \n'":"'").$str."'";

		if(!$JS_weapon)
			$JS_weapon	= "''";
		if(!$JS_armor)
			$JS_armor	= "''";
		if(!$JS_item)
			$JS_item	= "''";
		if(!$JS_other)
			$JS_other	= "''";
/*
		$JS_weapon	.= ($JS_weapon?" + \n'":"'None.")."<input type=\"hidden\" name=\"list_type\" value=\"weapon\">'";
		$JS_armor	.= ($JS_armor?" + \n'":"'None.")."<input type=\"hidden\" name=\"list_type\" value=\"armor\">'";
		$JS_item	.= ($JS_item?" + \n'":"'None.")."<input type=\"hidden\" name=\"list_type\" value=\"item\">'";
		$JS_other	.= ($JS_other?" + \n'":"'None.")."<input type=\"hidden\" name=\"list_type\" value=\"other\">'";
*/
if($this->Table) {
	$insert	= "insert = '".$this->TableInsert."'";
	$Table0	= "html = '".$this->Table."' + insert + html;";
	$Table1	= "html += insert + '</table>';";
} else {
	$None	= 'html = (html?"":"None.") + html;';
}
$js = <<< _JS_
<script type="text/javascript"><!--
function List{$this->name}(mode) {
switch(mode) {
case "weapon":
html = {$JS_weapon}; break;
case "armor":
html = {$JS_armor}; break;
case "item":
html = {$JS_item}; break;
case "other":
html = {$JS_other}; break;
}
return(html);
}
function ChangeType{$this->ID}() {
mode = document.getElementById('{$this->ID}').{$this->name}.options[document.getElementById('{$this->ID}').{$this->name}.selectedIndex].value
if(mode == 'all') {
html = List{$this->name}('weapon') + List{$this->name}('armor') + List{$this->name}('item') + List{$this->name}('other');
{$None}
hidden = '<input type="hidden" name="list_type" value="all">';
} else {
html = List{$this->name}(mode);
{$None}
hidden = '<input type="hidden" name="list_type" value="' + mode + '">';
}
{$insert}
{$Table0}
{$Table1}
html += hidden;
document.getElementById("{$Id}").innerHTML = html;
}
//--></script>
_JS_;
		return $js;
	}

	// �ǽ��ɽ��������
	function ShowDefault() {
		// JS��Ȥ�ʤ��Τ�����ɽ�����롣
		if($this->NoJS) {
			$list	= array_merge($this->weapon,$this->armor,$this->item,$this->other);
			foreach($list as $str)
				$open	.= $str."\n";
			if($this->Table) {
				$open	= $this->Table.$this->TableInsert.$open;
				$open	.= $this->TableInsert."</table>";
			}
			return $open;
		}

		switch($_POST["list_type"]) {
			default:
			case "weapon":
				$list	= $this->weapon;
				break;
			case "armor": $list	= $this->armor; break;
			case "item": $list	= $this->item; break;
			case "other": $list	= $this->other; break;
			case "all": $list	= array_merge($this->weapon,$this->armor,$this->item,$this->other); break;
		}
		foreach($list as $str)
			$open	.= $str."\n";

		switch($_POST["list_type"]) {
			case "armor": $open	.= "<input type=\"hidden\" name=\"list_type\" value=\"armor\">\n"; break;
			case "item": $open	.= "<input type=\"hidden\" name=\"list_type\" value=\"item\">\n"; break;
			case "other": $open	.= "<input type=\"hidden\" name=\"list_type\" value=\"other\">\n"; break;
			case "all": $open	.= "<input type=\"hidden\" name=\"list_type\" value=\"all\">\n"; break;
		}

		if($this->Table) {
			$open	= $this->Table.$this->TableInsert.$open;
			$open	.= $this->TableInsert."</table>";
		}

		return $open;
	}

	// SELECT�ܥå�����ɽ����
	function ShowSelect() {
		if($this->NoJS)
			return NULL;

		switch($_POST["list_type"]) {
			case "armor":	$armor	= " selected"; break;
			case "item":	$item	= " selected"; break;
			case "other":	$other	= " selected"; break;
			case "all":	$all	= " selected"; break;
		}

$html = <<< HTML
<form id="{$this->ID}"><select onchange="ChangeType{$this->ID}()" name="{$this->name}" style="margin-bottom:10px">
  <option value="weapon">���(weapon)</option>
  <option value="armor"{$armor}>�ɶ�(armor)</option>
  <option value="item"{$item}>�����ƥ�(---)</option>
  <option value="other"{$other}>����¾(other)</option>
  <option value="all"{$all}>����(all)</option>
</select></form>
HTML;

	return $html;
	}
}

?>
