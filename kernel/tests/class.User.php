<?php

require_once("/../punit/punit.php");
require_once("/../class.User.php");

class UserTest
{
	private $rafaelclp_id;

	function register()
	{
		MySQL::query("DELETE FROM `user`");

		$user = array();
		for ($i = 0; $i < 10; $i++)
		{
			$user[$i] = new User();
			$user[$i]->password = "rafael";
		}

		$user[0](array("username" => generate_random_string(USER_MIN_LENGTH, USER_MIN_LENGTH, USER_OPTIONAL_ALPHABET), "email" => "rafael.clp@hotmail.com"));
		$user[1](array("username" => generate_random_string(USER_MIN_LENGTH-1, USER_MIN_LENGTH-1, USER_REQUIRED_ALPHABET), "email" => "rafael.clp@hotmail.com"));
		$user[2](array("username" => generate_random_string(USER_MAX_LENGTH+1, USER_MAX_LENGTH+1, USER_REQUIRED_ALPHABET), "email" => "rafael.clp@hotmail.com"));
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
		$this->rafaelclp_id = $user[5]->id;
		if (!assertEquals($user[6]->register(), EMAIL_ALREADY_EXISTS)) return;
		if (!assertEquals($user[7]->register(), USER_ALREADY_EXISTS)) return;
		if (!assertEquals($user[8]->register(), REGISTER_SUCCESS)) return;
		if (!assertEquals($user[9]->register(), INVALID_PASSWORD)) return;
	}

	function login()
	{
		$user = new User();
		if (!assertFalse($user->login("rafaelclp", "rafolo"))) return;
		if (!assertTrue($user->login("rafaelclp", "rafael"))) return;
		if (!assertTrue($user->login("rafael.clp@hotmail.com", "rafael"))) return;
		if (!assertEquals($user->username, "rafaelclp")) return;
		if (!assertEquals($user->email, "rafael.clp@hotmail.com")) return;
		if (!assertFalse($user->login("rafael2.clp@hotmail.com", "rafael"))) return;
		if (!assertFalse($user->login("12391212", "rafael"))) return;
	}

	function get()
	{
		$user = new User();
		if (!assertFalse($user->get(0))) return;
		if (!assertFalse($user->get("rafoloclp"))) return;
		if (!assertFalse($user->get("rafolo.clp@hotmail.com"))) return;
		if (!assertFalse($user->get("rafael.clp@hotmail.com"))) return;
		if (!assertTrue($user->get("rafaelclp"))) return;
		if (!assertEquals($user->id, $this->rafaelclp_id)) return;
		if (!assertEquals($user->email, "rafael.clp@hotmail.com")) return;
		if (!assertTrue($user->get($this->rafaelclp_id))) return;
		if (!assertEquals($user->username, "rafaelclp")) return;
		if (!assertEquals($user->email, "rafael.clp@hotmail.com")) return;
	}

	function update()
	{
		$user = new User();
		$user->get("rafaelclp");
		$user->password = "rafolete";
		if (!assertTrue($user->update())) return;
		if (!assertFalse($user->login("rafaelclp", "rafael"))) return;
		if (!assertTrue($user->login("rafaelclp", "rafolete"))) return;
		$user->email = "rafael";
		if (!assertFalse($user->update())) return;
		$user->email = "rafael.clp@hotmail.com";
		if (!assertTrue($user->update())) return;
		$user->username = "12182812";
		if (!assertFalse($user->update())) return;
		$user->username = "rafaelclp";
		$user->password = generate_random_string(PASSWORD_MIN_LENGTH-1, PASSWORD_MIN_LENGTH-1, PASSWORD_ALPHABET);
		if (!assertFalse($user->update())) return;
	}

	function remove()
	{
		$user = new User();
		$user->get("rafaelclp");
		if (!assertTrue($user->remove())) return;
		if (!assertFalse($user->remove())) return;
		if (!assertEquals($user->register(), REGISTER_SUCCESS)) return;
		$user = new User();
		if (!assertTrue($user->remove("rafaelclp"))) return;
		if (!assertFalse($user->remove("rafaelclp"))) return;
	}

	function recover_password()
	{
		
	}
};

Assert::test_class("UserTest");

?>