<html><head><title>Verarbeite...</title>
<meta http-equiv="refresh" content="2; URL=index.php">
</head><body>
<h1> Verarbeite Anfrage!</h1>
<?php
include('./api/access/db.php');
session_start();

if ($debug) {
    ini_set('display_errors', 'On');
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';
}

if ( ! empty($_POST) ) {
	if (isset($_POST['username'] ) && isset( $_POST['password'])) {
		$con = new mysqli($dbHost, $dbUser, $dbPass, $dbDatabase);
		$stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		$result = $stmt->get_result();
		if ( mysqli_num_rows($result) == 1){
		$user = $result->fetch_object();
		if ( password_verify( $_POST['password'], $user->password)) {
			$_SESSION['user'] = $user->username;
			print "<h3>Erfolgreich eingeloggt</h3>";
			// with these two statements, the web app login on iphone gets permanent
			$cookieLifetime = 365 * 24 * 60 * 60; // A year in seconds
			setcookie(session_name(),session_id(),time()+$cookieLifetime); 
		} else { print "<h3>Falsches Passwort</h3>"; }} else { print "<h3>Falscher User?</h3>";}
	}
	
	if (isset($_POST['logout'])) {
	    print "Wird ausgeloggt...";
	    unset($_SESSION['user']);
	    session_destroy();
	}	
}

if($debug) {
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';
}



?>
</body></html>