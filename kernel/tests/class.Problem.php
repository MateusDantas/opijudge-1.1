<?php

require_once("/../punit/punit.php");
require_once("/../class.Problem.php");

class ProblemTest
{
	private $myid = null;
	private $userid = null;

	function add()
	{
		MySQL::query("DELETE FROM `problem`");
		MySQL::query("DELETE FROM `user`");

		$user = new User();
		$user(array("username" => "mateusgay", "email" => "mateusgay@gmail.com"));
		$user->password = "mateusgay";
		$user->register();
		$this->userid = $user->id;

		$problem = new Problem(generate_random_string(PROBLEM_NAME_MIN_LENGTH-1, PROBLEM_NAME_MIN_LENGTH-1), $user->id);
		if (!assertNotEquals($problem->add(), ADD_PROBLEM_SUCCESS)) return; // is_valid_name bug

		$problem = new Problem(generate_random_string(PROBLEM_NAME_MAX_LENGTH+1, PROBLEM_NAME_MAX_LENGTH+1), $user->id);
		if (!assertNotEquals($problem->add(), ADD_PROBLEM_SUCCESS)) return; // is_valid_name bug

		$problem = new Problem(" " . generate_random_string(PROBLEM_NAME_MIN_LENGTH-1, PROBLEM_NAME_MIN_LENGTH-1), $user->id);
		if (!assertNotEquals($problem->add(), ADD_PROBLEM_SUCCESS)) return; // should trim() name

		$problem = new Problem(generate_random_string(PROBLEM_NAME_MAX_LENGTH, PROBLEM_NAME_MAX_LENGTH) . " ", $user->id);
		if (!assertEquals($problem->add(), ADD_PROBLEM_SUCCESS)) return; // should trim() name

		if (!assertNotNull($problem->id)) return; // should learn how to use MySQL::insert_id()

		$this->myid = $problem->id;
		$problem->id = null;
		$problem->type = FULL_SCORE_PROBLEM_TYPE + BY_POINTS_PROBLEM_TYPE + 1;
		if (!assertNotEquals($problem->add(), ADD_PROBLEM_SUCCESS)) return; // should check type
	}

	function get()
	{
		$problem = new Problem();
		if (!assertFalse($problem->get(0))) return;
		if (!assertTrue($problem->get($this->myid))) return;
	}

	function get_submissions()
	{
		$problem = new Problem();
		$problem->get($this->myid);
		$submission = new Submission();
		$submission(array("problem_id"=>$this->myid, "user_id"=>$this->userid, "language" => LANG_CPP, "status" => "AC", "points" => 100, "time" => 0, "memory" => 0, "best_user_lang_ac" => true, "date" => date_now()));
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		$submissions = $problem->get_submissions();
		if (!assertEquals(sizeof($submissions), 1)) return;
		$submissions = $problem->get_submissions(100000);
		if (!assertEquals(sizeof($submissions), 1)) return;
	}

	function update()
	{
		$problem = new Problem();
		if (!assertTrue($problem->get($this->myid))) return;

		$problem->name = generate_random_string(PROBLEM_NAME_MAX_LENGTH+1, PROBLEM_NAME_MAX_LENGTH+1);
		if (!assertFalse($problem->update())) return;

		$problem->name = generate_random_string(PROBLEM_NAME_MAX_LENGTH, PROBLEM_NAME_MAX_LENGTH);
		if (!assertTrue($problem->update())) return;

		$problem->name = "this is my name";
		$problem->id = "0 or 1=1";
		if (!assertFalse($problem->update())) return; // simpliest sql injection ever
		$problem->id = "0' or '1'='1";
		if (!assertFalse($problem->update())) return;
	}

	function remove()
	{
		$problem = new Problem();
		if (!assertTrue($problem->get($this->myid))) return;
		if (!assertTrue($problem->remove())) return;
		if (!assertFalse($problem->remove())) return;
	}
};

Assert::test_class("ProblemTest");

?>