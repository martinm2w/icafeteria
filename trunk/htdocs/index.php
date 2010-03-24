<?php include('../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<ul class="menu">
<?php
$results = mysql_query(<<<SQL
SELECT items.*,
       item_type_name,
       req_ingreds.calories AS min_calories,
       all_ingreds.calories AS max_calories
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
        LEFT
        JOIN (   SELECT items.item_id AS item_id, SUM(ingreds.calories) AS calories
                   FROM       items
                         JOIN items_ingreds
                        USING (item_id)
                         JOIN ingreds
                        USING (ingred_id)
               GROUP BY items.item_id
             ) AS all_ingreds
       USING (item_id);
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

$prev_item_type_id = 0;
while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);

    if ($row['item_type_id'] != $prev_item_type_id) {
        echo "<li><h3>$row[item_type_name]</h3></li>";
        $prev_item_type_id = $row['item_type_id'];
    }

    echo '<li>';
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost</span>";

    if(isset($_SESSION['username'])) {
        $user_result = mysql_query("SELECT * FROM users WHERE username='$_SESSION[username]'", $db)
            or die('Select failed: ' . mysql_error());
        $user_row = mysql_fetch_assoc($user_result);

        if ($user_row['user_type_id'] == 2 || $user_row['user_type_id'] == 3) {
            echo "<span class=\"add-to-cart\"><a href=\"/manage/add_cart.php?item_id=$row[item_id]\">[add to cart]</a></span>";
        }
    }
	

    // sold out
    if($row['sold_out'] != 0) {
    	echo "  (!! Sold Out !!)";
    }
    //
    
    
    echo "</div>";

    echo '<div class="ingrediants">';

    $ingred_result = mysql_query("SELECT description FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = FALSE", $db)
        or die ('Select failed'.mysql_error());

    $first = true;
    while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
        echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
        $first = false;
    }

    echo '</div>';
    echo '<div class="ingrediants">';

    $ingred_opt_result = mysql_query("SELECT description FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = TRUE", $db)
        or die ('Select failed'.mysql_error());

    $first = true;
    while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
        echo ($first ? 'Optional Ingrediants: ' : ', ') . $ingred_opt_row['description'];
        $first = false;
    }

    echo '</div>';

    $calories = "$row[min_calories]&ndash;$row[max_calories]";
    echo "<div class=\"calories\">$calories cal</div>";
    echo '</li>';
}
?>
</ul>

<?php include('../include/footer.php'); ?>

