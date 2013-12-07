<?php

require_once "class.MySQL.php";
require_once "constants.php";
require_once "functions.php";

class Submission
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
			$keys = explode("|", SUBMISSION_COLUMNS);
			foreach ($keys as $key)
				self::$mysql_columns[$key] = true;
		}
	}

	static public function get_list($clauses="")
	{
		return MySQL::fetch_array("SELECT * FROM `submission` $clauses");
	}

	private function is_status_valid($status)
	{
		return between(strlen($status), STATUS_MIN_LENGTH, STATUS_MAX_LENGTH);
	}

	private function update_user_best_ac()
	{
		$submissions = MySQL::fetch_array("SELECT `id`, `points`, `time`, `best_user_lang_ac` FROM `submission` WHERE `user_id`=" . ((int)$this->user_id) . " AND `problem_id`=" . ((int)$this->problem_id) . " AND `language`=" . ((int)$this->language));

		$this->best_user_lang_ac = 0;
		if (sizeof($submissions) == 0) return;

		foreach ($submissions as $submission)
		{
			if ($submission["best_user_lang_ac"] == 1) $best_id = $submission["id"];
			$subs[] = array(-$submission["points"], $submission["time"], $submission["id"]);
		}
		sort($subs);

		if (!isset($best_id) || $subs[0][2] != $best_id)
		{
			if (isset($best_id))
				MySQL::query("UPDATE `submission` SET `best_user_lang_ac`=0 WHERE `id`=" . ((int)$best_id));
			MySQL::query("UPDATE `submission` SET `best_user_lang_ac`=1 WHERE `id`=" . ((int)$subs[0][2]));
			if ($this->id == $subs[0][2])
				$this->best_user_lang_ac = 1;
		}
	}

	private function get_data_error()
	{
		if (!is_language_known($this->language)) return UNKNOWN_LANGUAGE;
		if (!$this->is_status_valid($this->status)) return INVALID_SUBMISSION_STATUS;
		if (!between($this->points, 0, 100)) return INVALID_SUBMISSION_POINTS;
		return SUBMISSION_DATA_NO_ERROR;
	}

	public function add()
	{
		if ($this->id !== null || $this->problem_id === null || $this->user_id === null) return IMPLEMENTATION_ERROR;
		if (($error = $this->get_data_error()) != SUBMISSION_DATA_NO_ERROR) return $error;
		$this->date = date_now();

		$this->best_user_lang_ac = 0;
		if (!MySQL::insert("submission", $this->data)) return MYSQL_SERVER_ERROR;
		$this->id = MySQL::insert_id();

		$this->update_user_best_ac();
		return ADD_SUBMISSION_SUCCESS;
	}
	
	public function submit()
	{
		$client = stream_socket_client(JUDGE_HOST_CONFIG,$errno,$errorMessage);
		fwrite($client,JUDGE_SUBMISSION . ((int)$this->id));
		fclose($client);
	}

	public function get($id)
	{
		$this->data = MySQL::fetch("SELECT * FROM `submission` WHERE `id`=" . ((int)$id) . " LIMIT 1");
		return $this->data != false;
	}

	public function update()
	{
		if ($this->id === null || $this->problem_id === null || $this->user_id === null) return IMPLEMENTATION_ERROR;
		if (($error = $this->get_data_error()) != SUBMISSION_DATA_NO_ERROR) return $error;

		$this->best_user_lang_ac = 0;
		if (!MySQL::update("submission", $this->data)) return MYSQL_SERVER_ERROR;

		$this->update_user_best_ac();
		return UPDATE_SUBMISSION_SUCCESS;
	}

	public function remove($id=null)
	{
		if ($id === null && $this->id !== null)
			$id = $this->id;
		if (!MySQL::delete("submission", "WHERE `id`=" . ((int)$id))) return false;
		return MySQL::affected_rows() >= 1;
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
};

?>
