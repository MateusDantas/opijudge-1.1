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
	`visibility` TINYINT(1) NOT NULL DEFAULT 0,
	INDEX(problem_id,visibility,points,language),
	INDEX(user_id,problem_id,points)
) ENGINE=MyISAM;

