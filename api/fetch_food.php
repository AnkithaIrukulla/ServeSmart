<?php
include '../config/db.php';

// Remove expired food
$conn->query("UPDATE food SET status='sold' WHERE expiry < NOW()");

$location = $_GET['location'] ?? '';

if ($location) {
    $query = "SELECT * FROM food WHERE status='available' AND location LIKE '%$location%'";
} else {
    $query = "SELECT * FROM food WHERE status='available'";
}

$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>