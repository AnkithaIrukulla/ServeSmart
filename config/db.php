<?php
$conn = new mysqli("localhost", "root", "", "servesmart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>