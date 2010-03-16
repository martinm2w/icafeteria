CREATE TABLE items_ingred_opt (
    ingred_id INT NOT NULL AUTO_INCREMENT,
    item_id INT NOT NULL,
    description INT NOT NULL,
    calories INT NOT NULL,
    PRIMARY KEY (ingred_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id)
);

ALTER TABLE items_ingred
ADD calories INT NOT NULL,
CHANGE ingred_id ingred_id INT NOT NULL AUTO_INCREMENT;

ALTER TABLE items
DROP calories_low,
DROP calories_high,
ADD prep_time INT NOT NULL;

