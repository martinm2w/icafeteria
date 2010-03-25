<?php include('../../include/header.php'); ?>

<?php
switch ($_POST['action']) {
case 'add':
    mysql_query("INSERT INTO feedback(body) VALUES ('$_POST[body]')", $db)
        or die('Insert failed: ' . mysql_error());

    echo "<p>Thank you for your feedback.</p>";
    break;
}
?>

<div id="welcome">
    <p>Welcome to <span class="special">iCafeteria</span>.</p>
</div>

<p>iCafeteria is available Monday through Friday, 11:00am &ndash; 4:00pm.</p>

<p>&nbsp;</p>

<?php if (isset($_SESSION['user_id'])): ?>
<p>We welcome constructive feedback from our users. Thank you for your suggestions.</p>
<form action="/about/" method="POST">
    <textarea name="body" cols="50" rows="10"></textarea>
    <p><button type="submit" name="action" value="add">Submit feedback</button></p>
</form>
<?php endif; ?>

<?php include('../../include/footer.php'); ?>


