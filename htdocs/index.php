<?php include('../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Our Menu</h2>

<ul class="menu">
<?php
$results = mysql_query('select items.*, ifnull(req.calories, 0) as req_calories, ifnull(req.calories, 0) + ifnull(opt.calories, 0) as opt_calories from items left join (select items.item_id, sum(items_ingred.calories) as calories from items join items_ingred on items.item_id = items_ingred.item_id group by items.item_id) as req on items.item_id = req.item_id left join (select items.item_id, sum(items_ingred_opt.calories) as calories from items join items_ingred_opt on items.item_id = items_ingred_opt.item_id group by items.item_id) as opt on items.item_id = opt.item_id', $db)
    or die('Select failed: ' . mysql_error());

while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);

    echo '<li>';
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost</span></div>";

    $ingred_result = mysql_query("SELECT description FROM items_ingred WHERE items_ingred.item_id = $row[item_id]", $db)
        or die ('Select failed'.mysql_error());

    echo '<div class="ingrediants">';

    $first = true;
    while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
        echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
        $first = false;
    }

    $calories = "$row[req_calories]&ndash;$row[opt_calories]";

    echo '</div>';
    echo "<div class=\"calories\">$calories cal</div>";
    echo '</li>';
}
?>
</ul>

<?php include('../include/footer.php'); ?>

