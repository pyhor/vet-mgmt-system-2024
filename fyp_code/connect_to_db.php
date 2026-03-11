<?php
$servername = "localhost";
$username = "vet_system_db";
$password = "VsDB123$";
$dbname='vet_database';

// Create connection
$connect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connect) {
    echo "Connection failed: " . mysqli_connect_error();
    exit; // Stop execution if the connection fails
} else {
    //echo "Connected successfully"; // For debugging purposes
}
?>