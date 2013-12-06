<?php

require_once("constants.php");
require_once("class.MySQL.php");

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
	
	
	
	/**
	 * Based on array_data, register the user
	 * @return Return REGISTER_SUCCESS if the user
	 * was successfuly registered, otherwise return
	 * the error cause
	 */
	public function register()
	{
		if (!$this->is_password_valid($this->data["password"])) return INVALID_PASSWORD;
		if (!$this->is_username_valid($this->data["username"])) return INVALID_USER;
		if (!$this->is_email_valid($this->data["email"])) return INVALID_EMAIL;
		
		$this->hash_password();
		if ($this->exists($this->data["username"]) ) return USER_ALREADY_EXISTS;
		if ($this->exists($this->data["email"]) ) return EMAIL_ALREADY_EXISTS;
		if (!MySQL::insert("user",$this->data)) return MYSQL_SERVER_ERROR;
		
		return REGISTER_SUCCESS;
	}
	
	/**
	 * @param username_or_email User's username or email
	 * @param password User's password
	 * @return TRUE if login was successful or FALSE otherwise
	 */
	 public function login($username_or_email, $password)
	 {
		 
		if (!$this->exists($username_or_email)) return FALSE;
		 
		if ($this->is_email_valid($username_or_email)){
			$query_find_user = MySQL::fetch("SELECT * FROM user WHERE email='$username_or_email'");
		}else if ($this->is_username_valid($username_or_email)){
			$query_find_user = MySQL::fetch("SELECT * FROM user WHERE username='$username_or_email'");
		}else{
			return FALSE;
		}

		$hashed_password = $this->get_hashed_password($password, $query_find_user["salt"]);
		
		if($query_find_user["password"] !== $hashed_password ) return FALSE;
		
		return TRUE;
			
	 }
	 
	 /**
	  * Set the actual user
	  * @param username_or_id User's username or id
	  * @return TRUE if get was successful or FALSE otherwise
	  */
	 public function get($username_or_id)
	 {
		if (!$this->exists($username_or_id)) return FALSE;
		 
		if ($this->is_username_valid($username_or_id)){
			$query_find_user = MySQL::fetch("SELECT * FROM user WHERE username='$username_or_id'");
		}else{
			$query_find_user = MySQL::fetch("SELECT * FROM user WHERE id=$username_or_id");
		}
		
		$this->data["username"] = $query_find_user["username"];
		$this->data["email"] = $query_find_user["email"];
		$this->data["id"] = $query_find_user["id"];
		$this->data["password"] = $query_find_user["password"];
		
		return TRUE;
	 }
	 
	 /**
	  * Update the user table
	  * @return TRUE if update was successful or FALSE otherwise
	  */
	  public function update()
	  {
		  
		  if (!$this->is_username_invalid($this->data["username"])) return FALSE;
		  if (!$this->is_password_invalid($this->data["password"])) return FALSE;
		  if (!$this->is_email_valid($this->data["email"])) return FALSE;
		  
		  if ($this->exists($this->data["username"])) return FALSE;
		  if ($this->exists($this->data["email"])) return FALSE;
		  
		  if (!MySQL::update("user",$this->data)) return FALSE;
		  
		  return TRUE;
	  }
	  
	  /**
	   * Remove user
	   * @param username_or_email_or_id User's username, email or id
	   * @return TRUE if successfull or FALSE otherwise
	   */
	   public function remove($username_or_email_or_id)
	   {
		   
		  if (!$this->exists($username_or_email_or_id)) return FALSE;
		   
		  if ($this->is_username_valid($username_or_email_or_id)){
			  $query_find_user = MySQL::fetch("SELECT * FROM user where username='$username_or_email_or_id'");
		  }else if ($this->is_email_valid($username_or_email_or_id)){
			  $query_find_user = MySQL::fetch("SELECT * FROM user where email='$username_or_email_or_id'");
		  }else{
			  $query_find_user = MySQL::fetch("SELECT * FROM user where id=$username_or_email_or_id");
		  }
		  
		  $id_query = $query_find_user["id"];
		  if (!MySQL::delete("user", "WHERE id=$id_query")) return FALSE;
		
		  return TRUE;
	   }
	   
	   /**
	    * Generates a new password
	    * @return A new password
	    */
	    public function recover_password()
	    {
			$new_password = $this->get_random_password();
			$this->data["password"] = $new_password;
			$this->hash_password();
			if (!$this->update()) return ERROR_GENERATING_PASSWORD;
			
			return $new_password;
		}
	  
};
