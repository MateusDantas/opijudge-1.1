<?php

require_once("/../punit/punit.php");
require_once("/../class.User.php");

class UserTest
{
	function register()
	{
		MySQL::query("DELETE FROM `user`"); // so, use it only for local tests!!!
		if (!assertEquals(User::register(array("username"=>"rafaelclp", "email"=>"rafael.clp@hotmail.com", "password"=>"123456")), 0)) return;
		if (!assertEquals(User::register(array("username"=>"rafaelclp2", "email"=>"rafael.clp2@hotmail.com", "password"=>"1234567")), 0)) return;
		if (!assertEquals(User::register(array("username"=>"rafaelclp", "email"=>"aurora@hotmail.com", "password"=>"123456")), 1)) return;
		if (!assertEquals(User::register(array("username"=>"rafaelclp9", "email"=>"rafael.clp@hotmail.com", "password"=>"123456")), 2)) return;
		$reg_error = User::register(array("username"=>"rafaelclp", "email"=>"rafael.clp@hotmail.com", "password"=>"123456"));
		if (!assertTrue($reg_error == 3 || $reg_error == 1)) return; // an implementation that breaks after first error (1) is acceptable too
		if (!assertEquals(User::register(array("username"=>"123456", "email"=>"rafael.clp@hotmail.com", "password"=>"123456")), -1)) return;
		if (!assertEquals(User::register(array("username"=>"rafaelkka", "email"=>"rafaelclpsemnada@", "password"=>"123456")), -2)) return;
		if (!assertEquals(User::register(array("username"=>"rafaelclp", "email"=>"rafael.clp@hotmail.com", "password"=>"12345")), -3)) return;
	}

	function login()
	{
		if (!assertNotIdentical($user=User::login("rafaelclp", "123456"), false)) return;
		if (!assertTrue(isset($user["username"]) && isset($user["email"]))) return;
		if (!assertEquals($user["username"], "rafaelclp")) return;
		if (!assertEquals($user["email"], "rafael.clp@hotmail.com")) return;
		$user = User::get_last_login();
		if (!assertEquals($user["username"], "rafaelclp")) return;
		if (!assertEquals($user["email"], "rafael.clp@hotmail.com")) return;
		if (!assertTrue(isset($user["username"]) && isset($user["email"]))) return;
		if (!assertFalse(User::login("rafaelclp", "123345"))) return;
		if (!assertFalse(User::login("rafaelkk", "123456"))) return;
	}

	function get()
	{
		if (!assertNull(User::get("seila"))) return;
		$user = User::get("rafaelclp");
		$user2 = User::get("rafael.clp2@hotmail.com");
		if (!assertTrue(isset($user["username"]) && isset($user["email"]))) return;
		if (!assertEquals($user["username"], "rafaelclp")) return;
		if (!assertEquals($user["email"], "rafael.clp@hotmail.com")) return;
		if (!assertTrue(isset($user2["username"]))) return;
		if (!assertEquals($user2["username"], "rafaelclp2")) return;
		if (!assertNotNull(User::get($user["id"]))) return;
	}

	function edit()
	{
		$user = User::get("rafaelclp");
		$user["email"] = "rafolo.clp@hotmail.com";
		if (!assertTrue(User::edit($user))) return;
		$user["id"] = "rafael";
		if (!assertFalse(User::edit($user))) return;
		unset($user["id"]);
		if (!assertFalse(User::edit($user))) return;
		if (!assertEquals(User::get("rafaelclp")["email"], "rafolo.clp@hotmail.com")) return;
	}

	function recover_password()
	{
		$user = User::get("rafaelclp");
		if (!assertNotNull($new_password = User::recover_password($user["id"]))) return;
		$user2 = User::get("rafaelclp");
		if (!assertEquals($user2["password"], User::get_hashed_password($new_password, $user2["salt"]))) return;
	}

	function remove()
	{
		if (!assertTrue(User::remove("rafaelclp"))) return;
		if (!assertFalse(User::remove("rafaelclp"))) return;
		if (!assertNotIdentical(User::login("rafael.clp2@hotmail.com", "1234567"), false)) return;
		if (!assertTrue(User::remove("rafael.clp2@hotmail.com"))) return;
	}
};

Assert::test_class("UserTest");

?>