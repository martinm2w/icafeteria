<?php include('../../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<?php
switch ($_GET['action']) {
case 'add':
    // This is BAD!  We're not protecting against injection attacks.  We'll 
    // fix it later
    mysql_query("insert into items(item_name, cost, calories_low, calories_high, count) values ('$_GET[item_name]', $_GET[item_cost], $_GET[item_calories_low], $_GET[item_calories_high], $_GET[item_count])", $db)
        or die('Insert failed: ' . mysql_error());
    echo "<p>Your item was added to the menu.</p>";
    break;

case 'delete':
    // This is also BAD!
    mysql_query("delete from items where item_id = $_GET[item_id]", $db)
        or die('Delete failed: ' . mysql_error());
    echo "<p>Your item was removed from the menu.</p>";
    break;
}
?>

<h2>Manage Your Menu</h2>
<ul class="menu">
<?php
$results = mysql_query('select * from items', $db)
    or die('Select failed: ' . mysql_error());

while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);
    echo '<li>';
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost <a href=\"/manage/?action=delete&item_id=$row[item_id]\">[delete]</a></span></div>";

    $ingred_result = mysql_query("SELECT description FROM items_ingred WHERE items_ingred.item_id = $row[item_id]", $db)
        or die ('Select failed'.mysql_error());

    //echo "<font face = 'Times New Roman'> ";

    echo '<div class="ingrediants">';

    $first = true;
    while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
        echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
        $first = false;
    }

    $calories = $row['calories_low'];
    if (isset($row['calories_high'])) {
        $calories = "$calories&ndash;$row[calories_high]";
    }

    echo '</div>';
    echo "<div class=\"calories\">$calories cal</div>";
    echo '</li>';
}
?>
</ul>

<h2>Add a Menu Item</h2>
<form action="/manage/" method="GET">
    <input type="hidden" name="action" value="add">
    <p>Name: <input type="text" name="item_name" value=""></p>
    <p>Price: $<input style="width: 5em" type="text" name="item_cost" value=""></p>
    <p>Calories: <input style="width: 3em" type="text" name="item_calories_low" value=""> &ndash; <input style="width: 3em" type="text" name="item_calories_high" value=""></p>
    <p>Count: <input style="width: 3em" type="text" name="item_count" value="0"></p>
    <p><input type="submit" value="Add item"></p>
</form>

<?php include('../../include/footer.php'); ?>

