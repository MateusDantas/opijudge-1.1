<?php

// MySQL columns
define("USER_COLUMNS", "id|username|password|salt|email|access_level");
define("PROBLEM_COLUMNS", "id|user_id|name|type");
define("SUBMISSION_COLUMNS", "id|problem_id|user_id|language|status|points|time|memory|best_user_lang_ac|date");

// Strings
define("RANDOM_STRING_CHARSET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");

// General return status (errors)
define("IMPLEMENTATION_ERROR", -8);
define("MYSQL_SERVER_ERROR", -9);

// User configurations
define("USER_MIN_LENGTH", 4);
define("USER_MAX_LENGTH", 16);
define("USER_REQUIRED_ALPHABET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("USER_OPTIONAL_ALPHABET", "0123456789_.");

define("PASSWORD_ALPHABET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_.?!@#$%&*()-=+[{]};,<>");
define("PASSWORD_MIN_LENGTH", 6);
define("PASSWORD_MAX_LENGTH", 24);
define("RANDOM_PASSWORD_LENGTH", (int)((PASSWORD_MIN_LENGTH + PASSWORD_MAX_LENGTH) / 2));
define("PASSWORD_SALT_LENGTH", 8);

define("EMAIL_MAX_LENGTH", 48);

// User return status
define("WRONG_PASSWORD", 1);
define("WRONG_USERNAME", 2);
define("USER_ALREADY_EXISTS",3);
define("EMAIL_ALREADY_EXISTS",9);
define("INVALID_USER",4);
define("INVALID_EMAIL",5);
define("INVALID_PASSWORD",6);
define("REGISTER_SUCCESS",7);
define("LOGIN_SUCCESS",8);
define("ERROR_GENERATING_PASSWORD","");

define("TYPE_EMAIL", 1);
define("TYPE_USERNAME", 2);
define("TYPE_ID", 3);
define("INVALID_TYPE", 0);

?>
