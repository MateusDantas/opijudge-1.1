<?php

define("USER_COLUMNS", "id|username|password|salt|email|access_level");
define("WRONG_PASSWORD", 1);
define("WRONG_USERNAME", 2);
define("USER_ALREADY_EXISTS",3);
define("EMAIL_ALREADY_EXISTS",-3);
define("INVALID_USER",4);
define("INVALID_EMAIL",5);
define("INVALID_PASSWORD",6);
define("REGISTER_SUCCESS",7);
define("LOGIN_SUCCESS",8);
define("ERROR_GENERATING_PASSWORD","");
?>
