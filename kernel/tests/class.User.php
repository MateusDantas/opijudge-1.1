<?php

require_once("/../punit/punit.php");
require_once("/../class.User.php");

class UserTest
{
	function register()
	{
		MySQL::query("DELETE FROM `user`");

		$user = array();
		for ($i = 0; $i < 10; $i++)
		{
			$user[$i] = new User();
			$user[$i]->password = "rafael";
		}

		$user[0](array("username" => generate_random_string(MIN_USER_LENGTH, MIN_USER_LENGTH, USER_OPTIONAL_ALPHABET), "email" => "rafael.clp@hotmail.com"));
		$user[1](array("username" => generate_random_string(MIN_USER_LENGTH-1, MIN_USER_LENGTH-1, USER_REQUIRED_ALPHABET), "email" => "rafael.clp@hotmail.com"));
		$user[2](array("username" => generate_random_string(MAX_USER_LENGTH+1, MAX_USER_LENGTH+1, USER_REQUIRED_ALPHABET), "email" => "rafael.clp@hotmail.com"));
		$user[3](array("username" => "rafaclp", "email" => "rafael.clp"));
		$user[4](array("username" => "rafaclp", "email" => "rafael.clpaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@hotmail.com"));
		$user[5](array("username" => "rafaelclp", "email" => "rafael.clp@hotmail.com"));
		$user[6](array("username" => "rafoloclp", "email" => "rafael.clp@hotmail.com"));
		$user[7](array("username" => "rafaelclp", "email" => "rafolo.clp@hotmail.com"));
		$user[8](array("username" => "mcd10", "email" => "tantofaz@hotmail.com"));
		$user[9](array("username" => "mcd101", "email" => "tantofa2z@hotmail.com"));

		$user[9]->password = generate_random_string(PASSWORD_MIN_LENGTH-1, PASSWORD_MIN_LENGTH-1, PASSWORD_ALPHABET);

		if (!assertEquals($user[0]->register(), INVALID_USER)) return;
		if (!assertEquals($user[1]->register(), INVALID_USER)) return;
		if (!assertEquals($user[2]->register(), INVALID_USER)) return;
		if (!assertEquals($user[3]->register(), INVALID_EMAIL)) return;
		if (!assertEquals($user[4]->register(), INVALID_EMAIL)) return;
		if (!assertNull($user[5]->id)) return;
		if (!assertEquals($user[5]->register(), REGISTER_SUCCESS)) return;
		if (!assertNotNull($user[5]->id)) return;
		if (!assertEquals($user[6]->register(), EMAIL_ALREADY_EXISTS)) return;
		if (!assertEquals($user[7]->register(), USER_ALREADY_EXISTS)) return;
		if (!assertEquals($user[8]->register(), REGISTER_SUCCESS)) return;
		if (!assertEquals($user[9]->register(), INVALID_PASSWORD)) return;
	}

	function login()
	{
	}

	function get()
	{
	}

	function update()
	{
	}

	function remove()
	{
	}

	function recover_password()
	{
	}
};

Assert::test_class("UserTest");

?>