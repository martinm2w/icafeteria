<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'add':
    $open_order_results = mysql_query(<<<SQL
SELECT order_id
  FROM orders
 WHERE user_id = $_SESSION[user_id]
       AND NOT submitted
SQL
        , $db)
        or die('Select failed: ' . mysql_error());

    if (mysql_num_rows($open_order_results)) {
        $open_order_row = mysql_fetch_assoc($open_order_results);
        $open_order_id = $open_order_row['order_id'];
    }
    else {
        $new_order_results = mysql_query(<<<SQL
INSERT
  INTO orders(user_id)
VALUES ($_SESSION[user_id])
SQL
            , $db)
            or die('Insert failed: ' . mysql_error());

        $open_order_id = mysql_insert_id($db);
    }

    mysql_query(<<<SQL
INSERT
  INTO orders_items( item_id,
                     order_id,
                     count
                    )
VALUES ($_POST[item_id], $open_order_id, $_POST[count])
SQL
        , $db)
        or die('Insert failed: ' . mysql_error());

    if (isset($_POST['ingred_id'])) {
        foreach ($_POST['ingred_id'] as $i => $ingred_id) {
            mysql_query(<<<SQL
INSERT
  INTO orders_items_ingreds( item_id,
                             order_id,
                             ingred_id
                           )
VALUES ($_POST[item_id], $open_order_id, $ingred_id)
SQL
                , $db)
                or die('Insert failed: ' . mysql_error());
        }
    }

    echo "<p>An item was added to your cart.</p>";
    break;

case 'delete':
    mysql_query(<<<SQL
DELETE
  FROM orders_items_ingreds
 WHERE     item_id = $_POST[item_id]
       AND order_id = $_POST[order_id]
SQL
        , $db)
        or die('Delete failed: ' . mysql_error());

    mysql_query(<<<SQL
DELETE
  FROM orders_items
 WHERE     item_id = $_POST[item_id]
       AND order_id = $_POST[order_id]
SQL
        , $db)
        or die('Delete failed: ' . mysql_error());
    echo "<p>An item was removed from your cart.</p>";
    break;

case 'empty':
    $open_order_results = mysql_query(<<<SQL
SELECT order_id
  FROM orders
 WHERE user_id = $_SESSION[user_id]
       AND NOT submitted
SQL
        , $db)
        or die('Select failed: ' . mysql_error());

    $open_order_row = mysql_fetch_assoc($open_order_results);
    $open_order_id = $open_order_row['order_id'];

    mysql_query("DELETE FROM orders_items_ingreds WHERE order_id = $open_order_id", $db)
        or die('Delete failed: ' . mysql_error());
    mysql_query("DELETE FROM orders_items WHERE order_id = $open_order_id", $db)
        or die('Delete failed: ' . mysql_error());
    echo "<p>Your cart has been emptied.</p>";
    break;

case 'checkout':
    $open_order_results = mysql_query(<<<SQL
SELECT order_id
  FROM orders
 WHERE user_id = $_SESSION[user_id]
       AND NOT submitted
SQL
        , $db)
        or die('Select failed: ' . mysql_error());

    $open_order_row = mysql_fetch_assoc($open_order_results);
    $open_order_id = $open_order_row['order_id'];

    mysql_query(<<<SQL
UPDATE orders
   SET submitted = TRUE
 WHERE order_id = $open_order_id
SQL
        , $db)
        or die('Update failed: ' . mysql_error());
    echo "<p>This would have led to the check-out screen.</p>";
    break;
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Manage Your Cart</h2>

<ul class="menu">
<?php
$total_results = mysql_query(<<<SQL
SELECT order_id,
       order_calories.total_calories,
       SUM(count * cost) AS subtotal_cost,
       SUM(count * cost) * 0.08 AS total_tax,
       SUM(count * cost) * 1.08 AS total_cost_with_tax,
       MAX(prep_time) AS min_prep_time,
       SUM(count * prep_time) AS max_prep_time
  FROM       ( SELECT order_id,
                      SUM(count * calories) AS total_calories
                 FROM       users
                       JOIN orders
                      USING (user_id)
                       JOIN orders_items
                      USING (order_id)
                       JOIN items_ingreds
                      USING (item_id)
                      RIGHT
                       JOIN ingreds
                      USING (ingred_id)
                       LEFT
                       JOIN orders_items_ingreds
                      USING (item_id, order_id, ingred_id)
                WHERE     (    NOT optional
                            OR orders_items_ingreds.ingred_id IS NOT NULL
                          )
                      AND user_id = $_SESSION[user_id]
                      AND NOT submitted
                GROUP
                   BY order_id
             ) AS order_calories
        JOIN orders_items
       USING (order_id)
        JOIN items
       USING (item_id)
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

$results = mysql_query(<<<SQL
SELECT order_id,
       count,
       items.*,
       items_calories.calories
  FROM       ( SELECT order_id,
                      item_id,
                      SUM(count * calories) AS calories
                 FROM       users
                       JOIN orders
                      USING (user_id)
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
                      AND user_id = $_SESSION[user_id]
                      AND NOT submitted
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
    echo '<p>You have no items in your cart. <a href="/">Why not add some</a>?</p>';
}
else {
    $total_row = mysql_fetch_assoc($total_results);
    $prep_time = $total_row['min_prep_time'];

    if ($total_row['min_prep_time'] != $total_row['max_prep_time']) {
        $prep_time .= "&mdash;$total_row[max_prep_time]";
    }

    echo "<h3>Estimated prep-time: $prep_time minutes</h3>";
    echo "<h3>Total calories: $total_row[total_calories] cal</h3>";
    echo '<p>&nbsp;</p>';

    while (($row = mysql_fetch_assoc($results)) !== FALSE) {
        $cost = money_format('$%n', $row['cost']);

        echo '<li>';
        echo '<form action="/manage/cart.php" method="POST">';
        echo "<input type=\"hidden\" name=\"order_id\" value=\"$row[order_id]\">";
        echo "<input type=\"hidden\" name=\"item_id\" value=\"$row[item_id]\">";
        echo "<div class=\"title\">$row[count]x $row[item_name] <span class=\"cost\">@ $cost each <button type=\"submit\" name=\"action\" value=\"delete\">delete</button></span></div></form>";

        echo '<div class="ingrediants">';

        $ingred_result = mysql_query("SELECT description FROM items_ingreds JOIN ingreds USING (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND optional = FALSE", $db)
            or die ('Select failed'.mysql_error());

        $first = true;
        while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
            echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
            $first = false;
        }

        echo '</div>';
        echo '<div class="ingrediants">';

        $ingred_opt_result = mysql_query("SELECT description FROM orders_items_ingreds JOIN ingreds USING (ingred_id) JOIN items_ingreds USING (item_id, ingred_id) WHERE orders_items_ingreds.item_id = $row[item_id] AND orders_items_ingreds.order_id = $row[order_id] AND optional = TRUE", $db)
            or die ('Select failed'.mysql_error());

        $first = true;
        while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
            echo ($first ? 'Optional Ingrediants: ' : ', ') . $ingred_opt_row['description'];
            $first = false;
        }

        echo '</div>';

        echo "<div class=\"calories\">$row[calories] cal</div>";
        echo '</li>';
    }

    $subtotal_cost = money_format('$%n', $total_row['subtotal_cost']);
    $total_tax = money_format('$%n', $total_row['total_tax']);
    $total_cost_with_tax = money_format('$%n', $total_row['total_cost_with_tax']);

    echo '<p>&nbsp;</p>';
    echo "<p>Subtotal: $subtotal_cost</p>";
    echo "<p>Tax (8.00%): $total_tax</p>";
    echo "<h3>Total: $total_cost_with_tax</h3>";
    echo '';
    echo '<p><form action="/manage/cart.php" method="POST"><button type="submit" name="action" value="empty">Empty cart</button><button type="submit" name="action" value="checkout">Checkout</button></form></p>';
}
?>

<?php include('../../include/footer.php'); ?>

