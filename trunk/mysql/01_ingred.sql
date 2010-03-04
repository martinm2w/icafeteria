USE icafeteria;

CREATE TABLE items_ingred
(
item_id INT NOT NULL,
description VARCHAR(20) NOT NULL,
PRIMARY KEY (item_id, description),
FOREIGN KEY (item_id) REFERENCES items (item_id)
);

ALTER TABLE items
ADD count INT NOT NULL DEFAULT 0,
ADD calories_high INT,
CHANGE calories calories_low INT NOT NULL;

