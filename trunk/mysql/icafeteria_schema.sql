CREATE DATABASE IF NOT EXISTS icafeteria;
USE icafeteria;

CREATE TABLE IF NOT EXISTS user_types (
    user_type_id INT AUTO_INCREMENT,
    user_type_name VARCHAR(20) NOT NULL,
    PRIMARY KEY (user_type_id)
);

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT,
    user_type_id INT NOT NULL,
    username VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(45) NOT NULL,
    f_name VARCHAR(20) NOT NULL,
    l_name VARCHAR(20) NOT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_type_id) REFERENCES user_types(user_type_id)
);

CREATE TABLE IF NOT EXISTS item_types (
    item_type_id INT AUTO_INCREMENT,
    item_type_name VARCHAR(20) NOT NULL,
    PRIMARY KEY (item_type_id)
);

CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT,
    item_name VARCHAR(20) NOT NULL,
    cost FLOAT NOT NULL,
    sold_out BOOLEAN NOT NULL DEFAULT FALSE,
    prep_time INT NOT NULL,
    item_type_id INT NOT NULL,
    PRIMARY KEY (item_id),
    FOREIGN KEY (item_type_id) REFERENCES item_types(item_type_id)
);

CREATE TABLE IF NOT EXISTS ingreds (
    ingred_id INT AUTO_INCREMENT,
    description VARCHAR(20) NOT NULL,
    calories INT NOT NULL,
    PRIMARY KEY (ingred_id)
);

CREATE TABLE IF NOT EXISTS items_ingreds (
    item_id INT NOT NULL,
    ingred_id INT NOT NULL,
    optional BOOLEAN NOT NULL,
    PRIMARY KEY (item_id, ingred_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (ingred_id) REFERENCES ingreds(ingred_id)
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS orders_items (
    item_id INT NOT NULL,
    order_id INT NOT NULL,
    count INT NOT NULL,
    PRIMARY KEY (item_id, order_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE IF NOT EXISTS orders_items_ingreds (
    item_id INT NOT NULL,
    ingred_id INT NOT NULL,
    order_id INT NOT NULL,
    PRIMARY KEY (item_id, ingred_id, order_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (ingred_id) REFERENCES items(ingred_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT NOT NULL AUTO_INCREMENT,
    body TEXT NOT NULL,
    PRIMARY KEY (feedback_id)
);

