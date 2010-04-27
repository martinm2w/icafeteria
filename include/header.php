<?php
session_start();
$db = mysql_connect('localhost:3306', 'www') or die('Cannot connect to database: ' . mysql_error());

if ($_SERVER['SERVER_NAME'] == 'icafeteria.linuxbox') {
    mysql_select_db('icafeteria_local', $db) or die('Database switch failed: ' . mysql_error());
}
else {
    mysql_select_db('icafeteria', $db) or die('Database switch failed: ' . mysql_error());
}

$login_failed = false;

switch ($_POST['action']) {
case 'login':
    $login_failed = true;
    $user_result = mysql_query("SELECT user_id FROM users WHERE username='$_POST[username]' AND password='$_POST[password]'", $db)
        or die('Select failed: ' . mysql_error());

    if (mysql_num_rows($user_result)) {
        $login_failed = false;
        $user_row = mysql_fetch_assoc($user_result);
        $_SESSION['user_id'] = $user_row['user_id'];
    }

    break;

case 'logout':
    unset($_SESSION['user_id']);
    break;
}
?>

<!DOCTYPE html>

<html>
    <head>
        <link type="text/css" rel="stylesheet" href="/assets/style/layout.css">
        <link type="text/css" rel="stylesheet" href="/assets/style/theme.css">
        <title>iCafeteria</title>
    </head>
    <body>
        <div id="toolbar">
<?php if(isset($_SESSION['user_id'])): ?>
            <form action="/" method="POST">
                <input type="hidden" name="action" value="logout">
                <ul id="login">
                    <li class="login-info">
<?php
$user_result = mysql_query("SELECT * FROM users WHERE user_id=$_SESSION[user_id]", $db)
    or die('Select failed: ' . mysql_error());
$user_row = mysql_fetch_assoc($user_result);

echo "Logged in as $user_row[f_name] $user_row[l_name]";
?>
                    </li>
                    <li>
                        <button type="submit">Log Out</button>
                    </li>
                </ul>
            </form>
<?php else: ?>
            <form action="/" method="POST">
                <input type="hidden" name="action" value="login">
                <ul id="login">
                    <li>
                        <label for="username">Login Name:</label>
                        <input type="text" id="username" name="username" />
                    </li>
                    <li>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" />
                    </li>
                    <li>
                        <button type="submit">Log In</button>
                    </li>
                </ul>
            </form>
<?php endif; ?>
            <a href="/"><img id="logo" src="/assets/img/logo.png" alt="" /></a>
            <h1><a href="/">iCafeteria</a></h1>
            <ul id="main-nav">
                <li><a href="/">Menu</a></li>
                <li><a href="/about">About</a></li>
<?php if(isset($_SESSION['username'])): ?>
                <!-- <li><a href="/manage">Manage</a></li> -->
<?php endif; ?>

<?php
if(isset($_SESSION['user_id'])):
    $user_result = mysql_query("SELECT * FROM users WHERE user_id=$_SESSION[user_id]", $db)
        or die('Select failed: ' . mysql_error());
    $user_row = mysql_fetch_assoc($user_result);

    if ($user_row['user_type_id'] == 1):
?>
                <li><a href="/manage/item.php">Manage Menu Items</a></li>
                <li><a href="/manage/ingred.php">Manage Ingredients</a></li>
<?php
    endif;
    if ($user_row['user_type_id'] == 2 || $user_row['user_type_id'] == 3) {
        // We may turn this back on later. Shows an item count next to "Manage 
        // Cart".
        /*
        $cart_results = mysql_query(<<<SQL
SELECT SUM(count) as count
  FROM       orders
        JOIN orders_items
       USING (order_id);
SQL
            , $db)
            or die ('Select failed: ' . mysql_error());

        $cart_row = mysql_fetch_assoc($cart_results);
        $count = '';

        if (!is_null($cart_row['count'])) {
            $count = " ($cart_row[count])";
        }
         */

        echo "<li><a href=\"/manage/cart.php\">Manage Cart</a>$count</li>";
    }

    if ($user_row['user_type_id'] == 4):
?>
                <li><a href="/manage/prepare.php">Prepare Orders</a></li>
<?php
    endif;
endif;
?>
            </ul>
        </div>
        <div id="content">

