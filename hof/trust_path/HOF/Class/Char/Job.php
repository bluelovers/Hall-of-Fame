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
		} elseif ($this->_cache['gender'] != $this->char->gender)
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

			$this->char->img = ($this->jobdata['gender'][$this->char->gender]['img'] ? $this->jobdata['gender'][$this->char->gender]['img'] : $this->jobdata['img']);
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

		return (!$true && isset($this->char->icon)) ? $this->char->icon : $this->char->img;
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

		$this->char->maxhp = 100 * $coe['maxhp'] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevStr * $RevStr) / $div);
		$this->char->maxsp = 100 * $coe['maxsp'] * (1 + ($this->char->level - 1) / 49) * (1 + ($div - $RevInt * $RevInt) / $div);

		$this->char->maxhp = round($this->char->maxhp);
		$this->char->maxsp = round($this->char->maxsp);

		$ret = array(
			$this->_cache['hpsp'],
			array(
				'maxhp' => $this->char->maxhp,
				'maxsp' => $this->char->maxsp,
			),
			$this->char,
		);

		return $ret;
	}

}

