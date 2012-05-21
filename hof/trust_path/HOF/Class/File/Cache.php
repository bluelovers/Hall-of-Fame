<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_File_Cache
{

	public $fp;

	public $options = array(
		'autosave' => true,

		'format' => 'cache.%s.yml',

		'dataType' => 'yml',

		'path' => BASE_PATH_CACHE,

		'timeout' => 86400,
	);

	public $data = array();

	function __construct($options = array())
	{
		$this->options = array_merge($this->options, (array)$options);

		$this->data = new HOF_Class_Array($this->data);
		$this->options = new HOF_Class_Array($this->options);
	}

	function filname($id, $path = true)
	{
		$file = sprintf($this->options['format'], $id);

		if ($path)
		{
			$file = $this->options['path'] . $file;
		}

		return $file;
	}

	function load($id)
	{
		if (!HOF_Class_File::is_resource_file($this->fp[$id]) && $this->fp[$id] = HOF_Class_File::fplock_file($this->filname($id), 0, true))
		{
			$this->data[$id] = HOF_Class_Yaml::load($this->fp[$id]);

			if (empty($this->data[$id])) $this->data[$id] = array();

			$this->data[$id]['cache_id'] = $id;
			$this->data[$id]['cache_file'] = $this->filname($id, false);

			if ($this->data[$id]['cache_create'] <= 0) $this->data[$id]['cache_create'] = time();

			$this->data[$id]['cache_timeout'] = $this->data[$id]['cache_timeout'] ? min($this->data[$id]['cache_timeout'], $this->options['timeout']) : $this->options['timeout'];

			if ($this->data[$id]['cache_timestamp'] && REQUEST_TIME >= ($this->data[$id]['cache_timestamp'] + $this->data[$id]['cache_timeout']))
			{
				$this->data[$id]['data'] = false;

				$this->data[$id]['timeout'] = true;

				if ($this->data[$id]['cache_timeout'] <= 0 || $this->data[$id]['cache_timeout'] == $this->options['timeout'])
				{
					unset($this->data[$id]['cache_timeout']);
				}
			}
			else
			{
				unset($this->data[$id]['timeout']);
			}

			$this->_source_data_[$id] = (array)$this->data[$id];

			$this->data[$id] = new HOF_Class_Array($this->data[$id]);
		}

		return $this->data[$id]['data'];
	}

	function timeout($id, $timeout = 0)
	{
		if (!isset($this->data[$id]))
		{
			$this->load($id);
		}

		$this->data[$id]['cache_timeout'] = $timeout;
	}

	function data($id, $data = null, $save = false)
	{
		if (!isset($this->data[$id]))
		{
			$this->load($id);
		}

		if ($data !== null)
		{
			$this->data[$id]['data'] = $data;
		}

		if ($save)
		{
			$this->save($id);
		}

		return empty($this->data[$id]['data']) ? false : $this->data[$id]['data'];
	}

	function save($id)
	{
		if (HOF_Class_File::is_resource_file($this->fp[$id]) && $this->data[$id]->toArray() != $this->_source_data_[$id])
		{
			$this->data[$id]->cache_timestamp_last = (int)$this->data[$id]->cache_timestamp;
			$this->data[$id]->cache_timestamp = time();

			HOF_Class_Yaml::save(&$this->fp[$id], $this->data[$id]);
		}
	}

	function close($id)
	{
		HOF_Class_File::fpclose($this->fp[$id]);

		unset($this->fp[$id]);
	}

	function __destruct()
	{
		if ($this->options['autosave'])
		{
			foreach ((array)$this->fp as $id => $fp)
			{
				$this->save($id);
				$this->close($fp);
			}
		}
	}

}

