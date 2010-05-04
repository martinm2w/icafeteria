<?php include('../../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<?php
switch ($_POST['action']) {
case 'add':
    mysql_query(<<<SQL
INSERT
  INTO ingreds( description,
                calories
              )
VALUES ( '$_POST[description]',
         $_POST[calories]
       )
SQL
        , $db)
        or die('Insert failed: ' . mysql_error());

    echo "<p>Your ingrediant was added.</p>";
    break;

case 'delete':
    mysql_query("delete from ingreds where ingred_id = $_POST[ingred_id]", $db)
        or die('Delete failed: ' . mysql_error());
    echo "<p>Your ingrediant was removed.</p>";
    break;
}
?>

<h2>Add an Ingrediant</h2>
<div style="text-align: center;">
    <form action="/manage/ingred.php" method="POST">
        <input type="hidden" name="action" value="add">
        <p>Name: <input type="text" name="description" value=""></p>
        <p>Calories: <input style="width: 5em" type="text" name="calories" value="0"> cal</p>
        <p><input type="submit" value="Add ingrediant"></p>
    </form>
</div>

<h2>Manage Your Ingrediants</h2>
<ul class="menu">
<?php
$results = mysql_query('SELECT * FROM ingreds', $db)
    or die('Select failed: ' . mysql_error());

while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    echo '<li>';
    echo '<form action="/manage/ingred.php" method="POST">';
    echo "<input type=\"hidden\" name=\"ingred_id\" value=\"$row[ingred_id]\">";
    echo "<div class=\"title\">$row[description] <span class=\"cost\">&mdash; $cost <button type=\"submit\" name=\"action\" value=\"delete\">delete</button></span></div></form>";

    echo '<div class="ingrediants">';

    $item_result = mysql_query("SELECT item_name FROM items_ingreds join items using (item_id) WHERE ingred_id = $row[ingred_id]", $db)
        or die ('Select failed'.mysql_error());

    $first = true;
    while (($item_row = mysql_fetch_assoc($item_result)) != FALSE) {
        echo ($first ? 'Included in: ' : ', ') . $item_row['item_name'];
        $first = false;
    }

    echo '</div>';
    echo "<div class=\"calories\">$row[calories] cal</div>";
    echo '</li>';
}
?>
</ul>

<?php include('../../include/footer.php'); ?>

