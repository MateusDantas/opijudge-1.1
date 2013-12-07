<?php

require_once("/../punit/punit.php");
require_once("/../class.Submission.php");

class SubmissionTest
{
	private $myid;
	function add()
	{
		MySQL::query("DELETE FROM `submission`");
		//MySQL::query("DELETE FROM `user`");

		$submission = new Submission();
		$submission(array("id" => 5));
		if (!assertEquals($submission->add(), IMPLEMENTATION_ERROR)) return;

		$submission->id = null;
		if (!assertEquals($submission->add(), IMPLEMENTATION_ERROR)) return;

		$submission(array("problem_id" => 1, "user_id" => 2));
		if (!assertEquals($submission->add(), UNKNOWN_LANGUAGE)) return;

		$submission->language = LANG_CPP;
		if (!assertEquals($submission->add(), INVALID_SUBMISSION_STATUS)) return;

		$submission->status = "AC";
		$submission->points = 101;
		if (!assertEquals($submission->add(), INVALID_SUBMISSION_POINTS)) return;

		$submission->points = 80;
		$submission->time = 999;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		$this->myid = $submission->id;
	}

	function get()
	{
		$submission = new Submission();
		$submission->get($this->myid);

		if (!assertEquals($submission->problem_id, 1)) return;
		if (!assertEquals($submission->user_id, 2)) return;
		if (!assertEquals($submission->best_user_lang_ac, 1)) return;
	}

	function update_user_best_ac()
	{
		$submission = new Submission();
		$submission->get($this->myid);

		$submission->id = null;
		$submission->time = 900;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 1)) return;

		$submission->id = null;
		$submission->time = 900;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 0)) return;

		$submission->id = null;
		$submission->time = 920;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 0)) return;

		$submission->id = null;
		$submission->time = 900;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 0)) return;

		$submission->id = null;
		$submission->time = 899;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 1)) return;

		$submission->id = null;
		$submission->points = 90;
		$submission->time = 1000;
		if (!assertEquals($submission->add(), ADD_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 1)) return;
	}

	function update()
	{
		$submission = new Submission();
		$submission->get($this->myid);

		$submission->language = -1;
		if (!assertEquals($submission->update(), UNKNOWN_LANGUAGE)) return;
		$submission->language = LANG_CPP;

		$submission->status = "";
		if (!assertEquals($submission->update(), INVALID_SUBMISSION_STATUS)) return;
		$submission->status = "AC";

		$submission->points = 101;
		if (!assertEquals($submission->update(), INVALID_SUBMISSION_POINTS)) return;
		$submission->points = 90;

		if (!assertEquals($submission->update(), UPDATE_SUBMISSION_SUCCESS)) return;
		if (!assertEquals($submission->best_user_lang_ac, 1)) return;
	}

	function remove()
	{
		$submission = new Submission();
		$submission->get($this->myid);

		if (!assertFalse($submission->remove(0))) return;
		if (!assertTrue($submission->remove())) return;
		if (!assertFalse($submission->remove())) return;
	}
};

Assert::test_class("SubmissionTest");

?>