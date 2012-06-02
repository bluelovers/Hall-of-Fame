<?php

/**
 * @author bluelovers
 * @copyright 2012
 */


/**
 * new HOF_Class_Char_Job(array('job' => 100));
 */
class HOF_Class_Char_Job
{

	protected $char;
	public $jobdata;

	private $_cache;

	function __construct($char)
	{
		if (is_object($char))
		{
			$this->char = $char;
		}
		elseif (is_array($char))
		{
			$this->char = HOF_Class_Array::_toArrayObjectRecursive($char, 0);
		}
	}

	protected function _cache()
	{
		$change = false;

		if ($this->_cache['job'] != $this->char->job)
		{
			$change = true;
		}
		elseif ($this->_cache['gender'] != $this->char->gender)
		{
			$change = true;
		}

		$this->_cache['job'] = $this->char->job;
		$this->_cache['gender'] = $this->char->gender;

		return $change;
	}

	public function jobdata($job = null, $gender = null)
	{
		if ($job !== null)
		{
			$this->char->job = $job;
		}

		if ($gender !== null)
		{
			$this->char->gender = HOF_Helper_Math::minmax($gender, GENDER_UNKNOW, GENDER_GIRL);
		}

		if (!isset($this->jobdata) || $job !== null || $gender !== null || $this->_cache())
		{
			if (!$this->char->job)
			{
				throw new InvalidArgumentException("Job Null.");
			}

			$this->jobdata = HOF_Model_Data::getJobData($this->char->job);

			if (empty($this->jobdata))
			{
				throw new RuntimeException("Undefined Job {$this->char->job}.");
			}

			$this->char->job_name = ($this->jobdata['gender'][$this->char->gender]['job_name'] ? $this->jobdata['gender'][$this->char->gender]['job_name'] : $this->jobdata['job_name']);

			//debug($this->char->isMon(), $this->char->data);

			if (!($this->char->isMon() || $this->char->isChar() && $this->char->data['base']['type'] == 'mon'))
			{
				if ($this->jobdata['gender'][$this->char->gender]['img'])
				{
					$this->char->img = $this->jobdata['gender'][$this->char->gender]['img'];
				}
				elseif ($this->jobdata['img'])
				{
					$this->char->img = $this->jobdata['img'];
				}
				else
				{
					$this->char->img = $this->source()->img;
				}
			}
		}

		return $this->jobdata;
	}

	public function job($job = null)
	{
		if ($job !== null)
		{
			$this->jobdata($job);
		}

		return $this->char->job;
	}

	public function gender($gender = null)
	{
		if ($gender !== null)
		{
			$this->jobdata(null, $gender);
		}

		return $this->char->gender;
	}

	public function job_name($job = null, $gender = null)
	{
		$this->jobdata($job, $gender);

		return $this->char->job_name;
	}

	public function icon($job = null, $gender = null, $true = false)
	{
		$this->jobdata($job, $gender);

		return (!$true && !empty($this->char->icon)) ? $this->char->icon : $this->char->img;
	}

	public function icon_url($dir = HOF_Class_Icon::IMG_CHAR)
	{
		if ($dir === 0)
		{
			$dir = HOF_Class_Icon::IMG_CHAR;
		}
		elseif ($dir === 1)
		{
			$dir = HOF_Class_Icon::IMG_CHAR_REV;
		}

		return HOF_Class_Icon::getImageUrl($this->icon(), $dir);
	}

	/**
	 * HPとSPを計算して設定する
	 */
	public function hpsp() //
	{
		$MaxStatus = MAX_STATUS; //最高ステータス(じゃなくてもいいです)

		// 2回読み込んでるから直すべき
		$this->jobdata();

		/**
		 * $coe=array(HP, SP係数);
		 * @var array
		 */
		$coe = $this->jobdata['coe'];

		$div = $MaxStatus * $MaxStatus;
		$RevStr = $MaxStatus - $this->char->str;
		$RevInt = $MaxStatus - $this->char->int;

		$this->_cache['hpsp'] = array();

		$this->_cache['hpsp']['maxhp'] = $this->char->maxhp;
		$this->_cache['hpsp']['maxsp'] = $this->char->maxsp;

		$new_maxhp = 100 * $coe['maxhp'] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevStr * $RevStr) / $div);
		$new_maxsp = 100 * $coe['maxsp'] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevInt * $RevInt) / $div);

		$new_maxhp = round($new_maxhp);
		$new_maxsp = round($new_maxsp);

		$this->char->maxhp = max($this->char->maxhp, $this->_cache['hpsp']['maxhp'], $new_maxhp);
		$this->char->maxsp = max($this->char->maxsp, $this->_cache['hpsp']['maxsp'], $new_maxsp);

		$ret = array(
			$this->_cache['hpsp'],
			array(
				'maxhp' => $this->char->maxhp,
				'maxsp' => $this->char->maxsp,
				),
			//$this->char,
			);

		return $ret;
	}

	/**
	 * クラスチェンジ(転職)
	 * 装備をはずす。
	 */
	function job_change_to($job_to)
	{
		if (in_array($job_to, $this->job_change_list()))
		{
			$this->jobdata($job_to);
			$this->hpsp();

			/**
			 * 装備を全部解除
			 */
			$items = $this->char->unequip('all');

			return array(true, (array )$items);

			return true;
		}

		return false;
	}

	public function job_change_list($all_will = false)
	{
		$job_conditions = HOF_Model_Data::getJobConditions();

		$job_allow_change_to = array();

		if ($k = $job_conditions['job_from'][$this->char->job])
		{
			if ($all_will) return $k;

			foreach ($k as $job_to)
			{
				if ($v = $job_conditions['job_to'][$job_to][$this->char->job])
				{
					if ($this->char->level >= $v['lv'])
					{
						$job_allow_change_to[] = $job_to;
					}
				}
			}
		}

		return $job_allow_change_to;
	}

}
