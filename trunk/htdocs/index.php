<?php include('../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<ul class="menu">
<?php
$type_results = mysql_query(<<<SQL
SELECT item_type_id,
       item_type_name,
       item_count
  FROM       ( SELECT item_type_id,
                      COUNT(*) AS item_count
                 FROM       items
                       JOIN item_types
                      USING (item_type_id)
                GROUP
                   BY item_type_id
             ) AS item_type_counts
        JOIN item_types
       USING (item_type_id)
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

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
       USING (item_id)
 ORDER
    BY item_type_id
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

echo '<p>';
$first_type = TRUE;
while (($type_row = mysql_fetch_assoc($type_results)) !== FALSE) {
    if (!$first_type) {
        echo ' | ';
    }

    echo "<a href=\"#$type_row[item_type_id]\">$type_row[item_type_name]</a>";

    $first_type = FALSE;
}
echo '</p><p>&nbsp;</p>';

$prev_item_type_id = -1;
while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);

    if ($row['item_type_id'] != $prev_item_type_id) {
        if ($prev_item_type_id != -1) {
            echo '<p style="font-size: small;"><a href="#toolbar">Back to top</a></p>';
        }

        echo "<li><h2 id=\"$row[item_type_id]\">$row[item_type_name]</h2></li>";
        $prev_item_type_id = $row['item_type_id'];
    }

    echo '<li>';
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost</span>";

    if(isset($_SESSION['user_id'])) {
        $user_result = mysql_query("SELECT * FROM users WHERE user_id=$_SESSION[user_id]", $db)
            or die('Select failed: ' . mysql_error());
        $user_row = mysql_fetch_assoc($user_result);

        if ($row['sold_out']) {
            echo '<span class="add-to-cart"><span class="sold-out">Sold out</span></span>';
        }
        else {
            if ($user_row['user_type_id'] == 2 || $user_row['user_type_id'] == 3) {
                echo '<span class="add-to-cart">'; 
                echo "<a href=\"/manage/add_cart.php?item_id=$row[item_id]\">[add to cart]</a>";
                echo '</span>';
            }
        }
    }

    // sold out
    // if($row['sold_out'] != 0) {
            // echo "  (!! Sold Out !!)";
    // }
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
            <p style="font-size: small;"><a href="#toolbar">Back to top</a></p>
</ul>

<?php include('../include/footer.php'); ?>

