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
	protected $jobdata;

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

	private function _cache()
	{
		$change = false;

		if ($this->_cache['job'] != $this->char->job)
		{
			$change = true;
		} elseif ($this->_cache['gender'] != $this->char->gender)
		{
			$change = true;
		}

		$this->_cache['job'] = $this->char->job;
		$this->_cache['gender'] = $this->char->gender;

		return $change;
	}

	public function _jobdata($job = null, $gender = null)
	{
		if ($job !== null)
		{
			$this->char->job = $job;
		}

		if ($gender !== null)
		{
			$this->char->gender = (bool)$gender ? 1 : 0;
		}

		if (!isset($this->jobdata) || $job !== null || $gender !== null || $this->_cache())
		{
			if (!$this->char->job)
			{
				throw new RuntimeException("Can't find Chat Job.");
			}

			$this->jobdata = HOF_Model_Data::getJobData($this->char->job);

			$this->char->job_name = ($this->char->gender ? $this->jobdata['name_female'] : $this->jobdata['name_male']);

			$this->char->img = $this->char->icon = $this->jobdata['img_' . ($this->char->gender ? 'female' : 'male')];
		}

		return $this->jobdata;
	}

	public function job($job = null)
	{
		if ($job !== null)
		{
			$this->_jobdata($job);
		}

		return $this->char->job;
	}

	public function gender($gender = null)
	{
		if ($gender !== null)
		{
			$this->_jobdata(null, $gender);
		}

		return $this->char->gender;
	}

	public function job_name($job = null, $gender = null)
	{
		$this->_jobdata($job, $gender);

		return $this->char->job_name;
	}

	function icon($job = null, $gender = null)
	{
		$this->_jobdata($job, $gender);

		return $this->char->icon;
	}

	function icon_url($dir = HOF_Class_Icon::IMG_CHAR)
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
	function hpsp() //
	{
		$MaxStatus = MAX_STATUS; //最高ステータス(じゃなくてもいいです)

		// 2回読み込んでるから直すべき
		$this->_jobdata();

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

		$this->char->maxhp = 100 * $coe[0] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevStr * $RevStr) / $div);
		$this->char->maxsp = 100 * $coe[1] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevInt * $RevInt) / $div);

		$this->char->maxhp = round($this->char->maxhp);
		$this->char->maxsp = round($this->char->maxsp);

		return array(
			$this->_cache['hpsp'],
			array(
				'maxhp' => $this->char->maxhp,
				'maxsp' => $this->char->maxsp,
			),
			$this->char,
		);
	}

}

