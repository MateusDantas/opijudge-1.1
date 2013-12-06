<?php

// MySQL columns
define("USER_COLUMNS", "id|username|password|salt|email|access_level");

// Strings
define("RANDOM_STRING_CHARSET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");

// Return status (errors)
define("IMPLEMENTATION_ERROR", -8);
define("MYSQL_SERVER_ERROR", -9);

define("WRONG_PASSWORD", 1);
define("WRONG_USERNAME", 2);
define("USER_ALREADY_EXISTS",3);
define("INVALID_USER",4);
define("INVALID_EMAIL",5);
define("INVALID_PASSWORD",6);

// Users
define("MIN_USER_LENGTH", 4);
define("MAX_USER_LENGTH", 16);
define("USER_REQUIRED_ALPHABET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("USER_OPTIONAL_ALPHABET", "0123456789_.");

define("PASSWORD_ALPHABET", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_.?!@#$%&*()-=+[{]};,<>");
define("PASSWORD_MIN_LENGTH", 6);
define("PASSWORD_MAX_LENGTH", 24);
define("RANDOM_PASSWORD_LENGTH", (int)((PASSWORD_MIN_LENGTH + PASSWORD_MAX_LENGTH) / 2));
define("PASSWORD_SALT_LENGTH", 8);

define("EMAIL_MAX_LENGTH", 48);


?>
