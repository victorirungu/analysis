<?php
$servername = "localhost";
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

  // Create a new connection to the database
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check for database connection errors
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>
