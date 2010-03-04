<?php include('../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<h2>Our Menu</h2>

<ul class="menu">
<?php
$results = mysql_query('select * from items', $db)
    or die('Select failed: ' . mysql_error());

while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);

    // if ($row[item_id] == '7') {
        // echo "<p><font face='Helvetica'> $row[item_name] </t></t></t>: $ $cost </font><br/>";
        // echo "<font face='Times New Roman'>";
        // echo " : $row[calories_low] calories <br/> </font>";
    // }
    // else {

    //echo "<p><font face='Helvetica'> $row[item_name] </t></t></t>: $cost </font><br/>";
    echo '<li>';
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost</span></div>";

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

    // }
}
?>
</ul>

<?php include('../include/footer.php'); ?>

