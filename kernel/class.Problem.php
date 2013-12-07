<?php

require_once "class.MySQL.php";
require_once "constants.php";
require_once "functions.php";

class Problem
{
	static private $mysql_columns = null;
	private $data = array();

	/**
	 * Constructor: builds mysql columns for Problem class.
	 */
	public function __construct()
	{
		if (self::$mysql_columns === null)
		{
			$keys = explode("|", PROBLEM_COLUMNS);
			foreach ($keys as $key)
				self::$mysql_columns[$key] = true;
		}
	}

	public function __get($name)
	{
		if (is_string($name) && isset($this->data[$name]))
			return $this->data[$name];
		return null;
	}

	public function __set($name, $value)
	{
		if ($name == "all")
			$this->copy_data_from_array($value);
		else if (is_string($name) && key_exists($name, self::$mysql_columns))
			$this->data[$name] = $value;
	}

	/**
	 * Copies useful data from $data to $this->data.
	 */
	private function copy_data_from_array($data)
	{
		$this->data = $data;
		MySQL::clear_data($this->data, self::$mysql_columns, false);
	}
};

?>