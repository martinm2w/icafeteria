create database icafeteria;
use icafeteria;

create table items
(
item_id INT NOT NULL,
item_name VARCHAR(20) NOT NULL,
cost FLOAT NOT NULL,
calories FLOAT NOT NULL,
PRIMARY KEY (item_id)
);

create table user_types
(
user_type_id INT NOT NULL,
user_type_name VARCHAR(30) NOT NULL,
PRIMARY KEY (user_type_id)
);

create table user
(
user_id INT NOT NULL AUTO_INCREMENT,
user_type_id INT NOT NULL,
f_name VARCHAR(20) NOT NULL,
l_name VARCHAR(20) NOT NULL,
address1 VARCHAR(30),
address2 VARCHAR(20),
city VARCHAR(20),
state VARCHAR(20),
country VARCHAR(30),
zip_code INT,
phone_no VARCHAR(20),
PRIMARY KEY (user_id),
FOREIGN KEY (user_type_id) REFERENCES user_types (user_type_id)
);

create table orders
(
user_id INT NOT NULL,

orderitem_id INT NOT NULL,

order_id INT NOT NULL AUTO_INCREMENT,
totalquantity INT NOT NULL,
cost FLOAT NOT NULL,
tax FLOAT NOT NULL,
total_cost FLOAT NOT NULL,
total_calories INT NOT NULL,
order_status VARCHAR(20) NOT NULL,
order_timestamp timestamp default current_timestamp,
PRIMARY KEY (order_id),
FOREIGN KEY (orderitem_id ) REFERENCES orderitems(orderitem_id ),
FOREIGN KEY (user_id) REFERENCES user (user_id)
);


create table orderitems
(
orderitem_id INT NOT NULL AUTO_INCREMENT,  
user_id INT NOT NULL,
item_id INT NOT NULL,
quantity INT NOT NULL,
cost FLOAT NOT NULL,
tax FLOAT NOT NULL,
total_cost FLOAT NOT NULL,
calories INT NOT NULL,
PRIMARY KEY (orderitem_id, item_id),
FOREIGN KEY (item_id) REFERENCES items(item_id),
FOREIGN KEY (user_id) REFERENCES user (user_id)
);
