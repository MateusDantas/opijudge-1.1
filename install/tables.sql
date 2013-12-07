DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user`
(
	`id` INT(9) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`username` VARCHAR(16) NOT NULL,
	`password` CHAR(32) NOT NULL,
	`salt` CHAR(8) NOT NULL,
	`email` VARCHAR(48) NOT NULL,
	`access_level` TINYINT(2) NOT NULL DEFAULT 0,
	INDEX(username(4)),
	INDEX(email(8))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `problem`;
CREATE TABLE IF NOT EXISTS `problem`
(
	`id` INT(9) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`user_id` INT(9) NOT NULL,
	`name` VARCHAR(48) NOT NULL,
	`type` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `submission`;
CREATE TABLE IF NOT EXISTS `submission`
(
	`id` INT(9) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`problem_id` INT(9) NOT NULL,
	`user_id` INT(9) NOT NULL,
	`language` TINYINT(2) NOT NULL,
	`status` VARCHAR(64) NOT NULL,
	`points` SMALLINT(3) NOT NULL,
	`time` INT(9) NOT NULL,
	`memory` INT(9) NOT NULL,
	`best_user_lang_ac` TINYINT(1) NOT NULL DEFAULT 0,
	`date` DATETIME NOT NULL,
	INDEX(problem_id,best_user_lang_ac,points,time,date),
	INDEX(problem_id,best_user_lang_ac,language,points,time,date),
	INDEX(user_id,problem_id)
) ENGINE=MyISAM;
