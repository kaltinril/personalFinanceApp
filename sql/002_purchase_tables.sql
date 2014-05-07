CREATE TABLE IF NOT EXISTS `category` (
  `category_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_value` VARCHAR(45) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_date` DATETIME NOT NULL,
  `created_by` SMALLINT UNSIGNED NOT NULL,
  `modified_date` DATETIME NULL,
  `modified_by` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE INDEX `category_display_value_UNIQUE` (`display_value` ASC))
ENGINE = InnoDB;

insert into category (display_value, active, created_date, created_by) values ('Grocery', 1, now(), user());
insert into category (display_value, active, created_date, created_by) values ('Household', 1, now(), user());
insert into category (display_value, active, created_date, created_by) values ('Automotive', 1, now(), user());
insert into category (display_value, active, created_date, created_by) values ('Entertainment', 1, now(), user());

CREATE TABLE IF NOT EXISTS `location` (
  `location_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_value` VARCHAR(45) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_date` DATETIME NOT NULL,
  `created_by` SMALLINT UNSIGNED NOT NULL,
  `modified_date` DATETIME NULL,
  `modified_by` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE INDEX `location_display_value_UNIQUE` (`display_value` ASC))
ENGINE = InnoDB;

insert into location (display_value, active, created_date, created_by) values ('Safeway', 1, now(), user());
insert into location (display_value, active, created_date, created_by) values ('Fred Meyer', 1, now(), user());
insert into location (display_value, active, created_date, created_by) values ('Netflix', 1, now(), user());
insert into location (display_value, active, created_date, created_by) values ('Amazon', 1, now(), user());
insert into location (display_value, active, created_date, created_by) values ('Napa', 1, now(), user());

CREATE TABLE IF NOT EXISTS `purchase` (
  `purchase_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_date` DATE NOT NULL,
  `location_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `purchaser` SMALLINT UNSIGNED NOT NULL,
  `amount` INT NOT NULL,
  `created_date` DATETIME NOT NULL,
  `created_by` SMALLINT UNSIGNED NOT NULL,
  `modified_date` DATETIME NULL,
  `modified_by` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`purchase_id`),
  INDEX `fk_purchase_location_idx` (`location_id` ASC),
  INDEX `fk_purchase_category_idx` (`category_id` ASC),
  INDEX `fk_purchase_purch_id_idx` (`purchaser` ASC),
  INDEX `fk_purchase_created_user_idx` (`created_by` ASC),
  INDEX `fk_purchase_modified_user_idx` (`modified_by` ASC),
  CONSTRAINT `fk_purchase_location`
    FOREIGN KEY (`location_id`)
    REFERENCES `location` (`location_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_purchase_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_purchase_purch_id`
    FOREIGN KEY (`purchaser`)
    REFERENCES `users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_purchase_created_user`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_purchase_modified_user`
    FOREIGN KEY (`modified_by`)
    REFERENCES `users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `location_category` (
  `location_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`location_id`, `category_id`),
  INDEX `fk_category_id_map_idx` (`category_id` ASC),
  CONSTRAINT `fk_location_id_map`
    FOREIGN KEY (`location_id`)
    REFERENCES `location` (`location_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_id_map`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

insert into location_category (location_id, category_id) 
select l.location_id, c.category_id
from category c, location l
where l.display_value = 'Safeway'
and c.display_value in ('Grocery');

insert into location_category (location_id, category_id) 
select l.location_id, c.category_id
from category c, location l
where l.display_value = 'Fred Meyer'
and c.display_value in ('Grocery','Household','Automotive');

insert into location_category (location_id, category_id) 
select l.location_id, c.category_id
from category c, location l
where l.display_value = 'Napa'
and c.display_value in ('Automotive');

insert into location_category (location_id, category_id) 
select l.location_id, c.category_id
from category c, location l
where l.display_value = 'Netflix'
and c.display_value in ('Entertainment');

insert into location_category (location_id, category_id) 
select l.location_id, c.category_id
from category c, location l
where l.display_value = 'Amazon';