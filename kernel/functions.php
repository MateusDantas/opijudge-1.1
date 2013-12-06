<?php

require_once("constants.php");

/*
 * String functions
 */
function generate_random_string($minLen, $maxLen, $allowed_chars=RANDOM_STRING_CHARSET)
{
	$len = strlen($allowed_chars)-1;
	$random_string = "";
	for ($i = mt_rand($minLen, $maxLen); $i; $i--)
		$random_string .= $allowed_chars[mt_rand(0, $len)];
	return $random_string;
}

function is_email_valid($email)
{
	return strlen($email) <= EMAIL_MAX_LENGTH && filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_alphabet_map(&$map, $alphabet)
{
	for ($i = strlen($alphabet)-1; $i >= 0; $i--)
		$map[$alphabet[$i]] = true;
}

/*
 * Date and time functions
 */
function date_now()
{
	return date("Y-m-d H:i:s");
}

/*function difference_in_days($date1, $date2)
{
	if (($int = (new DateTime($date1))->diff(new DateTime($date2))) === false)
		return 0;
	return $int->days;
}*/

/*
 * Sets and intervals functions
 */
// eXclusive(ly) between
function xbetween($value, $min, $max)
{
	return $min < $value && $value < $max;
}

function between($value, $min, $max)
{
	return $min <= $value && $value <= $max;
}

function in($value, $set)
{
	if (is_string($set))
	{
		for ($i = strlen($set)-1; $i >= 0; $i--)
			if ($value == $set[$i])
				return true;
	}
	else
	{
		foreach ($set as $element)
			if ($value == $element)
				return true;
	}
	return false;
}

?>