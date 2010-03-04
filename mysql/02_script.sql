USE icafeteria;

DROP TABLE user_types;

ALTER TABLE `icafeteria`.`user` ADD COLUMN `username` VARCHAR(45) NOT NULL  AFTER `user_type_id` , ADD COLUMN `password` VARCHAR(45) NOT NULL  AFTER `username` ;

ALTER TABLE `icafeteria`.`user` 
DROP INDEX `user_type_id` ;

ALTER TABLE `icafeteria`.`user` CHANGE COLUMN `user_id` `user_id` INT(11) NOT NULL  ;

insert into `icafeteria`.`user` (`user_id`, `user_type_id`, `username`, `password`, `f_name`, `l_name`, `address1`, `address2`, `city`, `state`, `country`, `zip_code`, `phone_no`) values ('1', '1', 'harsh', 'harsh', 'Harsh', 'Seth', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
insert into `icafeteria`.`user` (`user_id`, `user_type_id`, `username`, `password`, `f_name`, `l_name`, `address1`, `address2`, `city`, `state`, `country`, `zip_code`, `phone_no`) values ('2', '1', 'amandeep', 'password', 'Amandeep', 'Singh', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
insert into `icafeteria`.`user` (`user_id`, `user_type_id`, `username`, `password`, `f_name`, `l_name`, `address1`, `address2`, `city`, `state`, `country`, `zip_code`, `phone_no`) values ('3', '1', 'chris', 'password', 'Chris', 'Bouchard', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
insert into `icafeteria`.`user` (`user_id`, `user_type_id`, `username`, `password`, `f_name`, `l_name`, `address1`, `address2`, `city`, `state`, `country`, `zip_code`, `phone_no`) values ('4', '1', 'ruobo', 'password', 'Ruobo', 'Wang', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
insert into `icafeteria`.`user` (`user_id`, `user_type_id`, `username`, `password`, `f_name`, `l_name`, `address1`, `address2`, `city`, `state`, `country`, `zip_code`, `phone_no`) values ('5', '1', 'hao', 'password', 'Hao', 'Shi', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `icafeteria`.`items_ingred` 
ADD INDEX `item_id` (`item_id` ASC) ;


insert into `icafeteria`.`items` (`item_id`, `item_name`, `cost`, `calories_low`, `count`, `calories_high`) values ('4', 'Pizza', '2.85', '350', '15', '850');
insert into `icafeteria`.`items` (`item_id`, `item_name`, `cost`, `calories_low`, `count`, `calories_high`) values ('5', 'Sandwich', '2.25', '150', '20', '450');
insert into `icafeteria`.`items` (`item_id`, `item_name`, `cost`, `calories_low`, `count`, `calories_high`) values ('6', 'Soup', '1.99', '200', '10', '500');
insert into `icafeteria`.`items` (`item_id`, `item_name`, `cost`, `calories_low`, `count`, `calories_high`) values ('7', 'Soda', '1.65', '400', '40', '800');
insert into `icafeteria`.`items` (`item_id`, `item_name`, `cost`, `calories_low`, `count`, `calories_high`) values ('3', 'Salad', '3.50', '100', '7', '600');


ALTER TABLE `icafeteria`.`items_ingred` ADD COLUMN `ingred_id` VARCHAR(45) NOT NULL  AFTER `item_id` 
, ADD INDEX `item_id` (`item_id` ASC) 
, DROP INDEX `item_id` 
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`ingred_id`) ;


insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('5', '2', 'Bread');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('4', '3', 'Cheese');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('4', '4', 'Olives');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('4', '5', 'Peppers');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('3', '6', 'Lettuce');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('5', '7', 'Chicken');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('6', '8', 'Croutons');
insert into `icafeteria`.`items_ingred` (`item_id`, `ingred_id`, `description`) values ('6', '9', 'Crackers');


update `icafeteria`.`items` set `count`='0' where `item_id`='3';


