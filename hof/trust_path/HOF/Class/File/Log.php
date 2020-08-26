<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_File_Log
{

	public $fp;

	public $options = array(
		'autosave' => true,

		'format' => 'log.%s.dat',

		'dataType' => 'log',

		'path' => BASE_PATH_LOG,

		'timeout' => 2592000,

		'max' => 150,
		'max_keep' => 100,
	);

	public $data = array();

	function __construct($options = array())
	{
		$this->options = array_merge($this->options, (array)$options);

		$this->data = new HOF_Class_Array($this->data);
		$this->options = new HOF_Class_Array($this->options);

		HOF::$_destruct_call[] = array($this, '__destruct');
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
			$this->data[$id]['data'] = array();

			rewind($this->fp[$id]);
			while (!feof($this->fp[$id]))
			{
				if ($str = trim(fgets($this->fp[$id])))
				{
					$this->data[$id]['data'][] = $str;
				}
			}
		}

		return $this->data[$id]['data'];
	}

	function timeout($id, $timeout = 0)
	{

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

			$this->data[$id]['changed']++;
		}

		if ($save)
		{
			$this->save($id);
		}

		return (array)$this->data[$id]['data'];
	}

	function save($id)
	{
		if ($this->data[$id]['changed'] && HOF_Class_File::is_resource_file($this->fp[$id]))
		{
			ftruncate($this->fp[$id], 0);
			rewind($this->fp[$id]);

			$this->data[$id]['data'] = (array)$this->data[$id]['data'];

			if (count($this->data[$id]['data']) > $this->options['max'])
			{
				$this->data[$id]['data'] = array_slice($this->data[$id]['data'], 0 - $this->options['max_keep']);
			}

			foreach($this->data[$id]['data'] as $line)
			{
				if ($line = trim(str_replace("\n", '', $line)))
				{
					fwrite($this->fp[$id], $line."\n");
				}
			}
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

