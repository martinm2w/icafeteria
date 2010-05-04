<?php
session_start();
$db = mysql_connect('localhost:3306', 'www') or die('Cannot connect to database: ' . mysql_error());

if ($_SERVER['SERVER_NAME'] == 'icafeteria.linuxbox') {
    mysql_select_db('icafeteria_local', $db) or die('Database switch failed: ' . mysql_error());
}
else {
    mysql_select_db('icafeteria', $db) or die('Database switch failed: ' . mysql_error());
}
?>

<!DOCTYPE html>

<html>
    <head>
        <title>iCafeteria</title>
    </head>
    <body>

        <ul class="menu">
<?php
$total_results = mysql_query(<<<SQL
SELECT order_id,
       f_name,
       l_name,
       SUM(count * cost) AS subtotal_cost,
       SUM(count * cost) * 0.08 AS total_tax,
       SUM(count * cost) * 1.08 AS total_cost_with_tax,
       NOW() + INTERVAL MAX(prep_time) MINUTE AS min_pickup_time,
       NOW() + INTERVAL SUM(count * prep_time) MINUTE AS max_pickup_time
  FROM       orders
        JOIN orders_items
       USING (order_id)
        JOIN items
       USING (item_id)
        JOIN users
       USING (user_id)
 WHERE order_id = $_GET[order_id]
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

$results = mysql_query(<<<SQL
SELECT order_id,
       count,
       items.*
  FROM       orders
        JOIN orders_items
       USING (order_id)
        JOIN items
       USING (item_id)
 WHERE order_id = $_GET[order_id]
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

if (!mysql_num_rows($results)) {
}
else {
    $total_row = mysql_fetch_assoc($total_results);
    $prep_time = $total_row['min_pickup_time'];

    if ($total_row['min_pickup_time'] != $total_row['max_pickup_time']) {
        $prep_time .= "&mdash;$total_row[max_pickup_time]";
    }

    echo '<h1>Order #' . str_pad($total_row['order_id'], 5, '0', STR_PAD_LEFT) . '</h1>';
    echo "<h3>$total_row[f_name] $total_row[l_name]</h3>";
    echo "<p>Expected pickup time: $prep_time</p>";
    echo '<p>&nbsp;</p>';

    while (($row = mysql_fetch_assoc($results)) !== FALSE) {
        $cost = money_format('$%n', $row['cost']);

        echo '<li>';
        echo "<div class=\"title\">$row[count]x $row[item_name]</div>";

        echo '<div class="ingrediants">';

        $ingred_opt_result = mysql_query("SELECT description FROM orders_items_ingreds JOIN ingreds USING (ingred_id) JOIN items_ingreds USING (item_id, ingred_id) WHERE orders_items_ingreds.item_id = $row[item_id] AND orders_items_ingreds.order_id = $row[order_id] AND optional = TRUE", $db)
            or die ('Select failed'.mysql_error());

        $first = true;
        while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
            echo ($first ? 'Optional Ingrediants: ' : ', ') . $ingred_opt_row['description'];
            $first = false;
        }

        echo '</div>';
        echo '</li>';
    }
}
?>
    </body>
</html>

