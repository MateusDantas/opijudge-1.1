<?php

require_once "class.MySQL.php";
require_once "constants.php";
require_once "functions.php";

class User
{
	static private $mysql_columns = null;
	private $data = array();

	/**
	 * Constructor: builds mysql columns for User class.
	 */
	public function __construct()
	{
		if (self::$mysql_columns === null)
			self::$mysql_columns = explode("|", USER_COLUMNS);
	}

	/**
	 * Informs if $username_or_email_or_id is an useranme, an email or an id.
	 *
	 * @return TYPE_EMAIL, TYPE_USERNAME, TYPE_ID or INVALID_TYPE
	 */
	private function get_type($username_or_email_or_id)
	{
		if ($this->is_valid_email($username_or_email_or_id))
			return TYPE_EMAIL;
		if ($this->is_valid_username($username_or_email_or_id))
			return TYPE_USERNAME;
		if (is_numeric($username_or_email_or_id))
			return TYPE_ID;
		return INVALID_TYPE;
	}

	/**
	 * Checks if $username_or_email_or_id is useranme, email or id and
	 * gets the correct clause to be used in a WHERE statement.
	 *
	 * @param username_or_email_or_id An username, email or id
	 * @return A clause in the format:
	 *		"`<username, email or id>`='$username_or_email_or_id'"
	 *		or null, if the parameter represents none of them.
	 */
	private function get_where_clause($username_or_email_or_id)
	{
		switch($this->get_type($username_or_email_or_password))
		{
		case TYPE_EMAIL:
			return "`email`='" . mysql_real_escape_string($username_or_email_or_id) . "'";
		case TYPE_USERNAME:
			return "`username`='" . mysql_real_escape_string($username_or_email_or_id) . "'";
		case TYPE_ID:
			return "`id`='$username_or_email_or_id'";
		}
		return null;
	}

	/**
	 * @param $password Any string that should represent a password.
	 * @return TRUE, if $password could be a password, or FALSE, otherwise.
	 */
	protected function is_password_valid($password)
	{
		if (!between(strlen($password), PASSWORD_MIN_LENGTH, PASSWORD_MAX_LENGTH))
			return false;

		generate_alphabet_map($is_valid, PASSWORD_ALPHABET);
		for ($i = strlen($password)-1; $i >= 0; $i--)
			if (!key_exists($password[$i], $is_valid))
				return false;

		return true;
	}

	/**
	 * @param $username Any string that should represent an username.
	 * @return TRUE, if $username could be an username, or FALSE, otherwise.
	 */
	protected function is_username_valid($username)
	{
		if (!between(strlen($username), MIN_USER_LENGTH, MAX_USER_LENGTH))
			return false;

		generate_alphabet_map($is_required, USER_REQUIRED_ALPHABET);
		generate_alphabet_map($is_valid, USER_OPTIONAL_ALPHABET);

		$contains_required = false;
		for ($i = strlen($username)-1; $i >= 0; $i--)
		{
			if (key_exists($username[$i], $is_required))
				$contains_required = true;
			else if (!key_exists($username[$i], $is_valid))
				return false;
		}
		return $contains_required;
	}

	/**
	 * @param $username Any string that should represent an email.
	 * @return TRUE, if $email could be an email, or FALSE, otherwise.
	 */
	protected function is_email_valid($email)
	{
		return is_email_valid($email);
	}

	/**
	 * @parameter $username_or_email_or_id A valid username, email or id.
	 * @return TRUE, if the username, email or id exists in the user table, or FALSE, otherwise.
	 */
	protected function exists($username_or_email_or_id)
	{
		
		$where_clause = $this->get_where_clause($username_or_email_or_id);
		return MySQL::fetch("SELECT COUNT(*) as amount FROM `user` WHERE $where_clause LIMIT 1")["amount"] >= 1;
	}

	/**
	 * @return A random salt.
	 */
	protected function get_random_salt()
	{
		return generate_random_string(PASSWORD_SALT_LENGTH, PASSWORD_SALT_LENGTH);
	}

	/**
	 * @return A random password.
	 */
	protected function get_random_password()
	{
		return generate_random_string(RANDOM_PASSWORD_LENGTH, RANDOM_PASSWORD_LENGTH);
	}

	/**
	 * @param $password The original password.
	 * @param $salt Concatenated to password before hashing.
	 * @return The hashed password.
	 */
	protected function get_hashed_password($password, $salt)
	{
		return md5($password . $salt);
	}

	/**
	 * Hashes the password in $self->data, if it isn't hashed yet (no salt exists).
	 *
	 * @param $force If TRUE, forces the hashing even if a salt already exists.
	 */
	protected function hash_password($force=false)
	{
		if (!$force && !key_exists($this->data["salt"]))
		{
			$this->data["salt"] = $this->get_random_salt();
			$this->data["password"] = $this->get_hashed_password($this->data["password"], $this->data["salt"]);
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
		if ($name == "password")
		{
			$this->data["password"] = $value;
			$this->hash_password(true);
		}
		else if (is_string($name) && key_exists($name, self::$mysql_columns))
			$this->data[$name] = $value;
	}
};

?>