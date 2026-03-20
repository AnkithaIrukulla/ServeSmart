<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$id = $_POST['id'];

// Prevent duplicates
if (!in_array($id, $_SESSION['cart'])) {
    $_SESSION['cart'][] = $id;
}

echo json_encode([
    "status" => "success",
    "cart_count" => count($_SESSION['cart'])
]);
?>