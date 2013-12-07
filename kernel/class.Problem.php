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
	public function __construct($name=null, $user_id=null, $type=FULL_SCORE_PROBLEM_TYPE)
	{
		if (self::$mysql_columns === null)
		{
			$keys = explode("|", PROBLEM_COLUMNS);
			foreach ($keys as $key)
				self::$mysql_columns[$key] = true;
		}
		$this->name = $name;
		$this->user_id = $user_id;
		$this->type = $type;
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
			$this->data[$name] = is_string($value) ? trim($value) : $value;
	}

	public function __invoke($data)
	{
		foreach ($data as $key => $value)
			if (key_exists($key, self::$mysql_columns))
				$this->data[$key] = $value;
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
	 * Checks if a problem name is valid.
	 *
	 * @param $name Problem name to be checked.
	 * @return TRUE if it is valid, or FALSE, otherwise.
	 */
	private function is_name_valid($name)
	{
		return between(strlen($name), PROBLEM_NAME_MIN_LENGTH, PROBLEM_NAME_MAX_LENGTH);
	}

	/**
	 * Adds the object problem
	 * @return ADD_PROBLEM_SUCCESS if successfull or the error message
	 * otherwise
	 */
	public function add()
	{
		if ($this->id !== null || $this->user_id === null)
			return IMPLEMENTATION_ERROR;
		if (!$this->is_name_valid($this->name))
			return INVALID_PROBLEM_NAME;
		if ($this->type != FULL_SCORE_PROBLEM_TYPE && $this->type != BY_POINTS_PROBLEM_TYPE)
			return IMPLEMENTATION_ERROR;
		if (!MySQL::insert("problem", $this->data))
			return MYSQL_SERVER_ERROR;
		$this->id = MySQL::insert_id();
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
		if (!is_numeric($this->id) || $this->id === null || $this->user_id === null)
			return IMPLEMENTATION_ERROR;
		if (!$this->is_name_valid($this->name))
			return INVALID_PROBLEM_NAME;
		if ($this->type != FULL_SCORE_PROBLEM_TYPE && $this->type != BY_POINTS_PROBLEM_TYPE)
			return IMPLEMENTATION_ERROR;
		if (!MySQL::update("problem", $this->data))
			return MYSQL_SERVER_ERROR;
		return UPDATE_PROBLEM_SUCCESS;
	}
	
	/**
	 * Remove some problem
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function remove($id=null)
	{
		if ($id === null && $this->id !== null)
			$id = (int)$this->id;

		if (!MySQL::delete("problem", "WHERE `id`=" . ((int)$id) . " LIMIT 1"))
			return false;
		return MySQL::affected_rows() >= 1;
	}
	
	/**
	 * Get the list of submissions of "this" problem
	 * @param $page Starting page (considering each page has $limit entries)
	 * @param $limit Amount of values to get.
	 * @param $language For which language we want to get submissions (LANG_ANY for any).
	 * @return Array of submissions of this problem.
	 */
	public function get_submissions($page=0, $limit=20, $language=LANG_ANY)
	{
		$where_clause = "WHERE `problem_id`=" . ((int)$this->id);
		$where_clause .= $language != LANG_ANY ? " AND `language`=" . ((int)$language) : "";
		$limit_clause = "LIMIT " . ($page*$limit) . ", $limit";
		return Submission::get_list("$where_clause $limit_clause");
	}

	/**
	 * Get the rank of submissions of "this" problem
	 * @param $page Starting page (considering each page has $limit entries)
	 * @param $limit Amount of values to get.
	 * @param $language For which language we want to get submissions (LANG_ANY for any).
	 * @return Array of submissions of this problem.
	 */
	public function get_rank($page=0, $limit=20, $language=LANG_ANY)
	{
		$where_clause = "WHERE `problem_id`=" . ((int)$this->id) . " AND `best_user_lang_ac`=1";
		$where_clause .= $language != LANG_ANY ? " AND `language`=" . ((int)$language) : "";
		$limit_clause = "LIMIT " . ($page*$limit) . ", $limit";
		return Submission::get_list("$where_clause ORDER BY `points` DESC, `time` ASC $limit_clause");
	}
};

?>
