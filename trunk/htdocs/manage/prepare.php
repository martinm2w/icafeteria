<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'delete':
    // This is also BAD!
    // mysql_query(<<<SQL
// DELETE
  // FROM orders_items_ingreds
 // WHERE     item_id = $_POST[item_id]
       // AND order_id = $_POST[order_id]
// SQL
        // , $db)
        // or die('Delete failed: ' . mysql_error());

    // mysql_query(<<<SQL
// DELETE
  // FROM orders_items
 // WHERE     item_id = $_POST[item_id]
       // AND order_id = $_POST[order_id]
// SQL
        // , $db)
        // or die('Delete failed: ' . mysql_error());
    // echo "<p>An item was removed from your cart.</p>";
    break;
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Next in Queue:</h2>

<ul class="menu">
</ul>

<?php include('../../include/footer.php'); ?>

