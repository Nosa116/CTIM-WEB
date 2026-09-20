<?php
// db_connect.php

$servername = "localhost";
$username = "u815552151_ctim"; // Default XAMPP username
$password = "christtempleintlministry@gmail.com"; // Default XAMPP password is empty
$dbname = "u815552151_database"; // The name of the database we created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
