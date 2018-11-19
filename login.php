<html><head><title>i3 Status System</title></head><body>
<?php
session_start();
$username = FALSE;

if ( isset( $_SESSION['user'] ) ) {
    print "Hey {$_SESSION['user']}! <br>";
    $username = $_SESSION['user'];?>
    <form method="post" action="proc.php">
<input type="hidden" name="logout" value="1"> <br>
<input type="submit" value="Log Out">
</form>
    <?php
} else {

    //header("Location: http://www.yourdomain.com/login.php");
    print "Please login! <br>";
    ?>
<form method="post" action="proc.php">
Username: <input type="text" name="username" placeholder="Username"> <br>
Password: <input type="password" name="password" placeholder="password"> <br>
<input type="submit" value="Log In">
</form>
<?php 
}
?>



</body>
</html>
