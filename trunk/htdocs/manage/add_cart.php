<?php include('../../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Add an Item to Your Cart</h2>

<ul class="menu">
    <li>

<?php
$results = mysql_query(<<<SQL
SELECT items.*,
       item_type_name,
       req_ingreds.calories AS min_calories
  FROM       items
        JOIN item_types
       USING (item_type_id)
        LEFT
        JOIN (   SELECT items.item_id AS item_id, SUM(ingreds.calories) AS calories
                   FROM       items
                         JOIN items_ingreds
                        USING (item_id)
                         JOIN ingreds
                        USING (ingred_id)
                  WHERE NOT items_ingreds.optional
               GROUP BY items.item_id
             ) AS req_ingreds
       USING (item_id)
 WHERE items.item_id = $_GET[item_id];
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

$row = mysql_fetch_assoc($results);
$cost = money_format('$%n', $row['cost']);

echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost</span></div>";

echo '<div class="ingrediants">';

$ingred_result = mysql_query("SELECT description FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = FALSE", $db)
    or die ('Select failed'.mysql_error());

$first = true;
while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
    echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
    $first = false;
}

echo '</div>';

$ingred_opt_result = mysql_query("SELECT ingred_id, description, calories FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = TRUE", $db)
    or die ('Select failed'.mysql_error());

echo "<form action=\"/manage/cart.php\" method=\"GET\">";
echo '<input type="hidden" name="action" value="add">';
echo '<p><div class="ingrediants">Optional Ingrediants:</div></p>';

while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
    echo "<p><input type=\"checkbox\" name=\"ingred_id\" value=\"$ingred_opt_row[ingred_id]\"> $ingred_opt_row[description] ($ingred_opt_row[calories] cal)</p>";
}

echo '<input type="submit" value="Add to Cart">';
echo '</form>';
?>

    </li>
</ul>

<?php include('../../include/footer.php'); ?>

