<?php

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "login";
$dbport = 3307;

// Create connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbport);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}


// $dbHost = "ysjcs.net";
// $dbUser = "karan.chawla";
// $dbPass = "UVVV6AKH";
// $dbName = "karanchawla_dlogin";
// $dbport = 3306;

// // Create connection
// $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbport);
// if (!$conn) {
//     die("Connection Failed: " . mysqli_connect_error());
// }

?>