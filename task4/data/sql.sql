CREATE DATABASE `aus_test` /*!40100 COLLATE 'utf8_general_ci' */;

CREATE TABLE `Users` (
	`uid` INT NOT NULL AUTO_INCREMENT COMMENT '��',
	`email` VARCHAR(100) NULL COMMENT 'Email',
	PRIMARY KEY (`uid`)
)
COMMENT='������������'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
