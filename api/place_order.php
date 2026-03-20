<?php
include '../config/db.php';
include '../includes/auth.php';

isNGO();
$user = currentUser();

session_start();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo json_encode([
        "status" => "error",
        "message" => "Cart is empty"
    ]);
    exit();
}

$total = 0;

foreach ($cart as $id) {
    $res = $conn->query("SELECT * FROM food WHERE id=$id AND status='available'");
    $row = $res->fetch_assoc();
    if ($row) {
        $total += $row['price'];
    }
}

// Create order
$conn->query("INSERT INTO orders(ngo_id,total) VALUES('{$user['id']}','$total')");
$order_id = $conn->insert_id;

// Insert order items
foreach ($cart as $id) {
    $conn->query("INSERT INTO order_items(order_id,food_id,quantity)
                  VALUES('$order_id','$id',1)");

    // Mark food as sold
    $conn->query("UPDATE food SET status='sold' WHERE id=$id");
}

// Clear cart
unset($_SESSION['cart']);

echo json_encode([
    "status" => "success",
    "message" => "Order placed successfully",
    "order_id" => $order_id
]);
?>