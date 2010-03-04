<?php
session_start();
$db = mysql_connect('localhost:3306', 'www') or die('Cannot connect to database: ' . mysql_error());
mysql_select_db('icafeteria', $db) or die('Database switch failed: ' . mysql_error());

$login_failed = false;

switch ($_GET['action']) {
case 'login':
    $login_failed = true;
    $result = mysql_query("SELECT 1 FROM user WHERE username='$_POST[username]' AND password='$_POST[password]'", $db)
        or die('Select failed: ' . mysql_error());

    if (mysql_num_rows($result)) {
        $login_failed = false;
        $_SESSION['username'] = $_POST['username'];
    }

    break;

case 'logout':
    unset($_SESSION['username']);
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
<?php if(isset($_SESSION['username'])): ?>
            <form action="/?action=logout" method="post">
                <ul id="login">
                    <li class="login-info">
<?php
$user_result = mysql_query("SELECT * FROM user WHERE username='$_SESSION[username]'", $db)
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
            <form action="/?action=login" method="post">
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
                <li><a href="/manage">Manage</a></li>
<?php endif; ?>
            </ul>
        </div>
        <div id="content">

