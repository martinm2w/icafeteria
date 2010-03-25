<?php include('../../include/header.php'); ?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<?php
switch ($_POST['action']) {
case 'add':
    // This is BAD!  We're not protecting against injection attacks.  We'll 
    // fix it later
    mysql_query(<<<SQL
INSERT
  INTO items( item_name,
              cost,
              prep_time,
              item_type_id)
VALUES ( '$_POST[item_name]',
         $_POST[item_cost],
         $_POST[prep_time],
         $_POST[item_type_id]
       )
SQL
        , $db)
        or die('Insert failed: ' . mysql_error());

    // Get the id generated by AUTO_INCREMENT
    $item_id = mysql_insert_id($db);

    if (isset($_POST['ingred_id'])) {
        foreach ($_POST['ingred_id'] as $i => $ingred_id) {
            $optional = $_POST['optional'][$i] ? 'TRUE' : 'FALSE';;
            mysql_query(<<<SQL
INSERT
  INTO items_ingreds( item_id,
                      ingred_id,
                      optional
                    )
VALUES ( $item_id,
         $ingred_id,
         $optional
       )
SQL
                , $db)
                or die('Insert failed: ' . mysql_error());
        }
    }

    echo "<p>Your item was added to the menu.</p>";
    break;

case 'delete':
    // This is also BAD!
    mysql_query("delete from items where item_id = $_POST[item_id]", $db)
        or die('Delete failed: ' . mysql_error());
    echo "<p>Your item was removed from the menu.</p>";
    break;
}
?>

<h2>Add a Menu Item</h2>
<div style="text-align: center;">
    <form action="/manage/item.php" method="POST">
        <input type="hidden" name="action" value="add">
        <p>Name: <input type="text" name="item_name" value=""></p>
        <p>Section: <select name="item_type_id">
<?php
$type_result = mysql_query("SELECT * from item_types", $db)
    or die ('Select failed'.mysql_error());
while (($type_row = mysql_fetch_assoc($type_result)) != FALSE) {
    echo "<option value=\"$type_row[item_type_id]\">$type_row[item_type_name]</option>";
}
?>
        </select></p>
        <p>Price: $<input style="width: 5em" type="text" name="item_cost" value="0.00"></p>
        <p>Prep time: <input style="width: 5em" type="text" name="prep_time" value="0"> minutes</p>
        <div id="ingreds-box">
            <a id="ingreds-add" href="#">[add ingrediant]</a>
        </div>
        <p><input type="submit" value="Add item"></p>
    </form>
</div>

<script type="text/javascript">
var ingredsBox = document.getElementById('ingreds-box');
var ingredsAdd = document.getElementById('ingreds-add');
var index = 0;

ingredsAdd.onclick = function () {
    var ingredSelect = document.createElement('select');
    var ingredP = document.createElement('p');
    var ingredDelA = document.createElement('a');
    var ingredCheck = document.createElement('input');
    var opt;

<?php
$ingred_result = mysql_query("SELECT * from ingreds", $db)
    or die ('Select failed'.mysql_error());
while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE):
?>
    opt = document.createElement('option');
    opt.text = "<?php echo "$ingred_row[description] ($ingred_row[calories] cal)"; ?>";
    opt.value = "<?php echo "$ingred_row[ingred_id]"; ?>";
    ingredSelect.add(opt);
<?php endwhile; ?>

    ingredSelect.name = 'ingred_id[' + index + ']';

    ingredCheck.name = 'optional[' + index + ']';
    ingredCheck.type = 'checkbox';
    ingredCheck.checked = false;

    ingredDelA.innerHTML = '[Delete]';
    ingredDelA.href = '#';
    ingredDelA.onclick = function () {
        ingredsBox.removeChild(ingredP);
        return false;
    };

    ingredP.appendChild(ingredSelect);
    ingredP.innerHTML += ' ';
    ingredP.appendChild(ingredCheck);
    ingredP.innerHTML += 'Optional ';
    ingredP.appendChild(ingredDelA);
    ingredsBox.insertBefore(ingredP, ingredsAdd);

    ++index;

    return false;
};
</script>

<h2>Manage Your Menu Items</h2>
<ul class="menu">
<?php
$results = mysql_query(<<<SQL
SELECT items.*,
       item_type_name,
       req_ingreds.calories AS min_calories,
       all_ingreds.calories AS max_calories
  FROM       items
        JOIN item_types
       USING (item_type_id)
        LEFT
        JOIN (   SELECT items.item_id AS item_id, SUM(ingreds.calories) AS calories
                   FROM       items
                         JOIN items_ingreds
                        USING (item_id)
                         JOIN ingreds
                        USING (ingred_id)
                  WHERE NOT items_ingreds.optional
               GROUP BY items.item_id
             ) AS req_ingreds
       USING (item_id)
        LEFT
        JOIN (   SELECT items.item_id AS item_id, SUM(ingreds.calories) AS calories
                   FROM       items
                         JOIN items_ingreds
                        USING (item_id)
                         JOIN ingreds
                        USING (ingred_id)
               GROUP BY items.item_id
             ) AS all_ingreds
       USING (item_id)
 ORDER
    BY item_type_id
SQL
    , $db)
    or die('Select failed: ' . mysql_error());

$prev_item_type_id = 0;
while (($row = mysql_fetch_assoc($results)) !== FALSE) {
    $cost = money_format('$%n', $row['cost']);

    if ($row['item_type_id'] != $prev_item_type_id) {
        echo "<li><h3>$row[item_type_name]</h3></li>";
        $prev_item_type_id = $row['item_type_id'];
    }

    echo '<li>';
    echo '<form action="/manage/item.php" method="POST">';
    echo "<input type=\"hidden\" name=\"item_id\" value=\"$row[item_id]\">";
    echo "<div class=\"title\">$row[item_name] <span class=\"cost\">&mdash; $cost <button type=\"submit\" name=\"action\" value=\"delete\"></span></div></form>";

    echo '<div class="ingrediants">';

    $ingred_result = mysql_query("SELECT description FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = FALSE", $db)
        or die ('Select failed'.mysql_error());

    $first = true;
    while (($ingred_row = mysql_fetch_assoc($ingred_result)) != FALSE) {
        echo ($first ? 'Ingrediants: ' : ', ') . $ingred_row['description'];
        $first = false;
    }

    echo '</div>';
    echo '<div class="ingrediants">';

    $ingred_opt_result = mysql_query("SELECT description FROM items_ingreds join ingreds using (ingred_id) WHERE items_ingreds.item_id = $row[item_id] AND items_ingreds.optional = TRUE", $db)
        or die ('Select failed'.mysql_error());

    $first = true;
    while (($ingred_opt_row = mysql_fetch_assoc($ingred_opt_result)) != FALSE) {
        echo ($first ? 'Optional Ingrediants: ' : ', ') . $ingred_opt_row['description'];
        $first = false;
    }

    echo '</div>';

    $calories = "$row[min_calories]&ndash;$row[max_calories]";
    echo "<div class=\"calories\">$calories cal</div>";
    echo '</li>';
}
?>
</ul>

<?php include('../../include/footer.php'); ?>

