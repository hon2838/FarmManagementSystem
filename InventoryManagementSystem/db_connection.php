<?php
$host = "localhost";
$username = "r1";
$password = "";
$database = "farm_management_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

