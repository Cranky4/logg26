CREATE DATABASE `aus_test` /*!40100 COLLATE 'utf8_general_ci' */;

CREATE TABLE `Users` (
	`uid` INT NOT NULL AUTO_INCREMENT COMMENT 'ИД',
	`email` VARCHAR(100) NULL COMMENT 'Email',
	PRIMARY KEY (`uid`)
)
COMMENT='Пользователи'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
