<?php
// Sample database connection (rename to connect.php and fill in your real details)

$HOSTNAME = '127.0.0.1';
$USERNAME = 'your_db_user';
$PASSWORD = 'your_db_password';
$DATABASE = 'lost_found';
$PORT     = 3307;

$con = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE, $PORT);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
