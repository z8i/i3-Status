<html><head><title>Add User</title></head><body>
<?php
include('./api/access/db.php');
session_start();

if ($debug) {
ini_set('display_errors', 'On');
echo '<pre>';
var_dump($_SESSION);
echo '</pre>';
}

if ( isset( $_SESSION['user'] ) ) {
    // If session is set
    if ($debug) { print "Hey {$_SESSION['user']}! <br /> "; }
    // prepare DB connection
    $con = new mysqli($dbHost, $dbUser, $dbPass, $dbDatabase);
} else {
    // If not, redirect to login
    $svname = $_SERVER['SERVER_NAME'];
    header("Location: https://$svname/index.php");
}

if (isset($_POST['username'])) {
    $error = false;
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (strlen($password)==0) {
        print "<h3>Passwort ist leer...</h3><br>";
        $error = true;
    }
    if (!(strcmp($adminKey, $_POST['adminkey'])==0)) {
        print "<h3>Admin-Key falsch...</h3><br>";
        $error = true;        
    }
        
    if(!$error) { //check if user does not exist
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();
        if (isset($user->username)) {
            if ($user->username == $_POST['username'] ) {
            echo "<h3>User existiert bereits!</h3> <br>";
            $error = true;
            }
        }
        
    }    
    if (!$error) { // DB Insert interaction
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $stmt = $con->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $username, $password_hash);
        $result = $stmt->execute();
        if ($result) {
            echo "<h3>Erfolgreich registriert</h3>";
        } else {
            echo "<h3>Fehler beim speichern</h3>";
        }
        $stmt->close(); //close DB connection
        $con->close();
    }
    
}

?>
<div>
<a href="index.php">Back to Main Page</a><br>
<h1>Add user to database</h1>

<form method="post" action="update.php">
Username: <input type="text" name="username" placeholder="username"> <br>
Password: <input type="password" name="password" placeholder="password"> <br>
Admin-Key:<input type="password" name="adminkey" placeholder="adminkey"> <br>
<input type="submit" value="Add User">
</form>
</div>
</body>
</html>
