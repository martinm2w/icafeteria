USE icafeteria;

INSERT INTO user_types(user_type_name)
SELECT 'Manager'
UNION
SELECT 'Student'
UNION
SELECT 'Faculty'
;

INSERT INTO users(user_type_id, username, password, f_name, l_name)
SELECT 1, 'harsh', 'password', 'Harsh', 'Seth'
UNION
SELECT 1, 'amandeep', 'password', 'Amandeep', 'Sahani'
UNION
SELECT 1, 'chris', 'password', 'Chris', 'Bouchard'
UNION
SELECT 1, 'ruobo', 'password', 'Ruobo', 'Wang'
UNION
SELECT 1, 'hao', 'password', 'Hao', 'Shi'
UNION
SELECT 2, 'student', 'password', 'Test', 'Student'
;

INSERT INTO item_types(item_type_name)
SELECT 'Appetizers'
UNION
SELECT 'Sandwiches'
UNION
SELECT 'Dinners'
;

INSERT INTO items(item_name, cost, prep_time, item_type_id)
SELECT 'Salad', 3.50, 5, 1
UNION
SELECT 'Hamburger', 5.00, 5, 2
UNION
SELECT 'Chicken Sandwich', 2.25, 10, 2
UNION
SELECT 'Turkey Dinner', 10.00, 10, 3
UNION
SELECT 'Pizza Slice', 3.00, 20, 3
;

INSERT INTO ingreds(description, calories)
SELECT 'Lettuce', 10
UNION
SELECT 'Tomato Slices', 30
UNION
SELECT 'Cucumber Slices', 30
UNION
SELECT 'Croutons', 100
UNION
SELECT 'House Dressing', 200
UNION
SELECT 'Wheat Roll', 50
UNION
SELECT 'Pickel Slices', 50
UNION
SELECT 'Mayonaise', 100
UNION
SELECT 'All-Beef Patty', 500
UNION
SELECT 'Chicken Slices', 400
UNION
SELECT 'Sliced Turkey', 300
UNION
SELECT 'Turkey Gravy', 100
UNION
SELECT 'Mashed Potatoes', 300
UNION
SELECT 'Cranberry Sauce', 100
UNION
SELECT 'Pizza Crust', 100
UNION
SELECT 'Tomato Sauce', 50
UNION
SELECT 'Cheese', 100
UNION
SELECT 'Extra Cheese', 100
UNION
SELECT 'Pepperoni', 100
;

INSERT INTO items_ingreds(item_id, ingred_id, optional)
SELECT 1, 1, FALSE
UNION
SELECT 1, 2, FALSE
UNION
SELECT 1, 3, FALSE
UNION
SELECT 1, 4, TRUE
UNION
SELECT 1, 5, TRUE
UNION
SELECT 2, 6, FALSE
UNION
SELECT 3, 6, FALSE
UNION
SELECT 2, 7, TRUE
UNION
SELECT 3, 7, TRUE
UNION
SELECT 2, 8, TRUE
UNION
SELECT 3, 8, TRUE
UNION
SELECT 2, 9, FALSE
UNION
SELECT 3, 10, FALSE
UNION
SELECT 4, 11, FALSE
UNION
SELECT 4, 12, FALSE
UNION
SELECT 4, 13, FALSE
UNION
SELECT 4, 14, TRUE
UNION
SELECT 5, 15, FALSE
UNION
SELECT 5, 16, FALSE
UNION
SELECT 5, 17, FALSE
UNION
SELECT 5, 18, TRUE
UNION
SELECT 5, 19, TRUE
;

