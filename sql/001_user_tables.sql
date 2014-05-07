CREATE TABLE IF NOT EXISTS `users` (
  `user_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(50) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `users_unq` (`user_name` ASC))
ENGINE = InnoDB;

CREATE TABLE `sessions` (
  `user_id` smallint(5) unsigned NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `ses_user_id_fk` (`user_id`),
  CONSTRAINT `ses_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB;
