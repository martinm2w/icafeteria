<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'pay':
    mysql_query(<<<SQL
UPDATE orders
   SET paid = TRUE
 WHERE order_id = $_POST[order_id]
SQL
        , $db)
        or die('Update failed: ' . mysql_error());
    echo "<p>An order was marked as paid.</p>";
    break;
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Manage Orders:</h2>

<?php
$results = mysql_query(<<<SQL
SELECT orders.order_id,
       CONCAT(users.f_name, ' ', users.l_name) AS name,
       ROUND(cost, 2) AS cost
  FROM       orders
        JOIN ( SELECT order_id,
                      SUM(count * cost) AS cost
                 FROM       orders
                       JOIN orders_items
                      USING (order_id)
                       JOIN items
                      USING (item_id)
                GROUP
                   BY order_id
             ) AS orders_cost
       USING (order_id)
        JOIN users
       USING (user_id)
 WHERE     completed
       AND NOT paid
 ORDER
    BY order_time
SQL
    , $db)
    or die('Select failed: ' . mysql_error());
?>

<table width="100%">
    <thead>
        <tr><th>Order ID</th><th>Name</th><th>Total</th><th><th><th></th></tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
<?php
while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    echo "<tr><td>" . str_pad($row['order_id'], 5, '0', STR_PAD_LEFT) . "</td><td>$row[name]</td><td>$$row[cost]</td><td><a href=\"label.php?order_id=$row[order_id]\" target=\"_blank\">[Print Label]</a></td><td><a href=\"receipt.php?order_id=$row[order_id]\" target=\"_blank\">[Print Receipt]</a></td><td><form action=\"manage.php\" method=\"POST\"><input type=\"hidden\" name=\"order_id\" value=\"$row[order_id]\"><button type=\"submit\" name=\"action\" value=\"pay\">Mark Paid</button></form></td></tr>";
}
?>
    </tbody>
</table>

<?php include('../../include/footer.php'); ?>

