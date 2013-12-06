<?php

class User
{
	static private $mysql_columns = null;
	private $data = array();

	public function __construct()
	{
		if (self::$mysql_columns === null)
			self::$mysql_columns = explode("|", USER_COLUMNS);
	}

	public function __get($name)
	{
		if (is_string($name) && isset($this->data[$name]))
			return $this->data[$name];
		return null;
	}

	public function __set($name, $value)
	{
		if (is_string($name) && array_key_exists($name, self::$mysql_columns))
			$this->data[$name] = $value;
	}
};