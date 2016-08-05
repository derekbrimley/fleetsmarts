<?php
$servername = "localhost";
$username = "dbrimley";
$password = "8t@w7Z0z;?&2";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
?>