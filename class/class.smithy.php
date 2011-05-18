<?php
// ���결
class Item {
	var $item;

	var $base,$refine;
	var $option0,$option1,$option2;

	var $type;

	function Item($no) {
		mt_srand();
		$this->SetItem($no);
	}
//////////////////////////////////////////////////
//	�����ƥब�Ϥ��줿���ǡ�������Ϥ���?
	function SetItem($no) {
		if(!$no) return false;
		$this->item	= $no;

		$this->base	= substr($no,0,4);//�����ƥ�δ����ֹ�
		// ��ϣ��
		$this->refine	= (int)substr($no,4,2);
		if(!$this->refine)
			$this->refine	= 0;
		// �ղ�ǽ��
		$this->option0	= substr($no,6,3);
		$this->option1	= substr($no,9,3);
		$this->option2	= substr($no,12,3);

		if($item = LoadItemData($this->base)) {
			$this->type	= $item["type"];
		}
	}
//////////////////////////////////////////////////
//	�����ƥ������롣
	function CreateItem() {
		$this->refine	= false;
		$this->option0	= false;
		$this->option1	= false;
		$this->option2	= false;
		list($low,$high)	= ItemAbilityPossibility($this->type);

		// 2:3:4
		// �ղ�ǽ�Ϥ��Ĥ���Ψ��
		$prob	= mt_rand(1,9);
		switch($prob) {
			case 1:
			case 2:
			case 3:
				$AddLow	= true;
				break;
			case 4:
			case 5:
			case 6:
				$AddHigh	= true;
				break;
			case 7:
			case 8:
			case 9:
				$AddLow	= true;
				$AddHigh	= true;
				break;
		}

		// array_rand() ����̯�ʤΤǷɱ󤹤롣

		if($AddHigh) {
			$prob	= mt_rand(0,count($high)-1);
			$this->option1	= $high["$prob"];
		}
		if($AddLow) {
			$prob	= mt_rand(0,count($low)-1);
			$this->option2	= $low["$prob"];
		}
	}
//////////////////////////////////////////////////
//	�ü�ʤ��졩3���ܤ��ղá�
	function AddSpecial($opt) {
		$this->option0	= $opt;
	}
//////////////////////////////////////////////////
//	��ϣ��ǽ��ʪ���ɤ�����
	function CanRefine() {
		$possible	= CanRefineType();
		if (REFINE_LIMIT <= $this->refine)
			return false;
		else if(in_array($this->type,$possible))
			return true;
		else
			return false;
	}
//////////////////////////////////////////////////
//	��ϣ�򤹤�
	function ItemRefine() {
		if($this->RefineProb($this->refine)) {
			print("+".$this->refine." -> ");
			$this->refine++;
			print("+".$this->refine." <span class=\"recover\">Success</span>&nbsp;!<br />\n");
			return true;
		} else {
			print("+".$this->refine." -> ");
			print("+".($this->refine + 1)." <span class=\"dmg\">Failed</span>.<br />\n");
			return false;
		}
	}
//////////////////////////////////////////////////
//	��ϣ���̤���ϣ�������ݤ��Ȥ��γ�Ψ
	function RefineProb($now) {
		$prob	= mt_rand(0,99);
		//return true;// �����ȼ�������Ψ100%
		switch($now) {
			case 0:
			case 1:
			case 2:
			case 3:
				return true;
			case 4:
				if($prob < 60)
				return true;
			case 5:
				if($prob < 40)
				return true;
			case 6:
				if($prob < 40)
				return true;
			case 7:
				if($prob < 20)
				return true;
			case 8:
				if($prob < 20)
				return true;
			case 9:
				if($prob < 10)
				return true;
		}
		return false;
	}
//////////////////////////////////////////////////
//	�����ƥ���֤���
	function ReturnItem() {
		// ��ϣ�⥪�ץ�����̵��������Ƭ4ʸ�������֤���
		if(!$this->refine && !$this->option0 && !$this->option1 && !$this->option2 )
			return $this->base;
		
		// ���ʤ��Ȥ���ϣ����Ƥ��뤫�����ץ����ͭ����
		$item	= $this->base.
				sprintf("%02d",$this->refine).
				$this->option0.
				$this->option1.
				$this->option2;
		return $item;
	}
}
?>