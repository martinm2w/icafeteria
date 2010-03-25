<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'add':
    // This is BAD!  We're not protecting against injection attacks.  We'll 
    // fix it later
    mysql_query(<<<SQL
INSERT
  INTO orders_items( item_id,
                     order_id,
                     count
                    )
SELECT $_POST[item_id],
       order_id,
       $_POST[count]
  FROM orders
 WHERE user_id = $_SESSION[user_id]
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
SELECT $_POST[item_id],
       order_id,
       $ingred_id
  FROM orders
 WHERE user_id = $_SESSION[user_id]
SQL
                , $db)
                or die('Insert failed: ' . mysql_error());
        }
    }

    echo "<p>An item was added to your cart.</p>";
    break;

case 'delete':
    // This is also BAD!
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
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<ul class="menu">
<?php
$total_results = mysql_query(<<<SQL
SELECT order_id,
       order_calories.total_calories,
       SUM(count * cost) AS total_cost,
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
                 FROM        users
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
    while (($row = mysql_fetch_assoc($results)) !== FALSE) {
        $cost = money_format('$%n', $row['cost']);

        echo '<li>';
        echo '<form action="/manage/cart.php" method="POST"><input type="hidden" name="action" value="delete">';
        echo "<input type=\"hidden\" name=\"order_id\" value=\"$row[order_id]\">";
        echo "<input type=\"hidden\" name=\"item_id\" value=\"$row[item_id]\">";
        echo "<div class=\"title\">$row[count]x $row[item_name] <span class=\"cost\">@ $cost each <input type=\"submit\" value=\"delete\"></span></div></form>";

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
}
?>

<?php include('../../include/footer.php'); ?>

