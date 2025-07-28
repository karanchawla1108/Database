<?php


$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "gymm";
$dbport = 3307;

// Create connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbport);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
} else {
    echo "Connected to the database";
}


// $dbHost = "ysjcs.net";
// $dbUser = "karan.chawla";
// $dbPass = "UVVV6AKH";
// $dbName = "karanchawla_Gym";
// $dbport = 3306;

// // Create connection
// $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbport);
// if (!$conn) {
//     die("Connection Failed: " . mysqli_connect_error());
// }


?>