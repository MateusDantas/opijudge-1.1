<?php

require_once "class.MySQL.php";
require_once "constants.php";
require_once "functions.php";
require_once "class.Submission.php";

class Problem
{
	static private $mysql_columns = null;
	private $data = array();

	/**
	 * Constructor: builds mysql columns for Problem class.
	 */
	public function __construct($name, $type=FULL_SCORE_PROBLEM_TYPE)
	{
		if (self::$mysql_columns === null)
		{
			$keys = explode("|", PROBLEM_COLUMNS);
			foreach ($keys as $key)
				self::$mysql_columns[$key] = true;
		}
		$this->data["name"] = $name;
		$this->data["type"] = $type;
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
	
	/**
	 * Adds the object problem
	 * @return ADD_PROBLEM_SUCCESS if successfull or the error message
	 * otherwise
	 */
	public function add()
	{
		if (!$this->is_name_valid($this->name)) {
			return INVALID_PROBLEM_NAME;
		}
		if (!MySQL::insert("problem", $this->data)) {
			return MYSQL_SERVER_ERROR;
		}
		return ADD_PROBLEM_SUCCESS;
	}
	
	/**
	 * Get some problem by id
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function get($id)
	{
		$this->data = MySQL::fetch("SELECT * FROM `problem` WHERE `id`=" . ((int)$id) . " LIMIT 1");
		return $this->data != false;
	}
	
	/**
	 * Update the SQL with the new data
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function update()
	{
		$now_id = (int)$this->id;
		if (!MySQL::update("problem", $this->data,"WHERE id=$now_id"))
			return FALSE;
			
		return TRUE;
	}
	
	/**
	 * Remove some problem
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function remove($id)
	{
		if (!MySQL::delete("problem","WHERE id=" . ((int)$id)))
			return FALSE;
			
		return TRUE;
	}
	
	/**
	 * Get the list of submissions of some problem
	 * @param id id of the problem
	 * @param limit Limit of results
	 * @return Array of submissions of some problem
	 */
	public function get_submissions($id, $limit=NO_LIMIT_PROBLEM)
	{
		if ($limit === NO_LIMIT_PROBLEM){
			$array_submissions = Submission::get_list("WHERE id=$id");
		} else {
			$array_submissions = Submission::get_list("WHERE id=$id LIMIT=$limit");
		}
		
		return $array_submissions;
	}
};

?>
