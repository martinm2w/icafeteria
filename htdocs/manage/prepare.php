<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'complete':
    mysql_query(<<<SQL
UPDATE orders
   SET completed = TRUE
 WHERE order_id = $_POST[order_id]
SQL
        , $db)
        or die('Update failed: ' . mysql_error());
    echo "<p>An order was completed.</p>";
    break;
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Next Order in Queue:</h2>

<ul class="menu">

<?
$results = mysql_query(<<<SQL
SELECT order_id,
       count,
       items.*,
       items_calories.calories
  FROM       ( SELECT order_id,
                      item_id,
                      SUM(count * calories) AS calories
                 FROM       ( SELECT order_id
                                FROM orders
                               WHERE     submitted
                                     AND NOT completed
                               ORDER
                                  BY order_time
                               LIMIT 1
                            ) AS first_order
                       JOIN orders_items
                      USING (order_id)
                       JOIN items
                      USING (item_id)
                       JOIN items_ingreds
                      USING (item_id)
                       JOIN ingreds
                      USING (ingred_id)
                       LEFT
                       JOIN orders_items_ingreds
                      USING (order_id, item_id, ingred_id)
                WHERE     (    NOT optional
                            OR orders_items_ingreds.ingred_id IS NOT NULL
                          )
                GROUP
                   BY item_id
             ) AS items_calories
        JOIN items
       USING (item_id)
        JOIN orders
       USING (order_id)
        JOIN orders_items
       USING (order_id, item_id);
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

if (!mysql_num_rows($results)) {
    echo '<p>You have no orders in your queue.</p>';
}
else {
    while (($row = mysql_fetch_assoc($results)) !== FALSE) {
        $cost = money_format('$%n', $row['cost']);

        echo '<li>';
        echo "<div class=\"title\">$row[count]x $row[item_name]</div>";

        $ingred_opt_result = mysql_query("SELECT description FROM orders_items_ingreds JOIN ingreds USING (ingred_id) JOIN items_ingreds USING (item_id, ingred_id) WHERE orders_items_ingreds.item_id = $row[item_id] AND orders_items_ingreds.order_id = $row[order_id] AND optional = TRUE", $db)
            or die ('Select failed'.mysql_error());

        echo '<p>Optional Ingrediants:</p>';

        if (mysql_num_rows($ingred_opt_result)) {
            while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
                echo "<p>$ingred_opt_row[description]</p>";
            }
        }
        else {
            echo '<p>None</p>';
        }

        echo '</li>';
    }
}
?>

</ul>

<?php
if (mysql_num_rows($results)):
    $order_results = mysql_query(<<<SQL
SELECT order_id
  FROM orders
 WHERE     submitted
       AND NOT completed
 ORDER
    BY order_time
 LIMIT 1
SQL
        , $db)
        or die('Select failed: ' . mysql_error());
    $order_row = mysql_fetch_assoc($order_results);
?>
<p>&nbsp;</p>
<form action="prepare.php" method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order_row['order_id']; ?>">
    <p align="center"><button type="submit" name="action" value="complete">This order is complete</button></p>
</form>
<?php endif; ?>

<?php include('../../include/footer.php'); ?>

