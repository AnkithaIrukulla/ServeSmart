<?php
include '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $order_id = $_POST['order_id'] ?? 0;
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $ngo_id = $_SESSION['user']['id'];

    if (!$order_id) {
        die("Invalid Order ID");
    }

    // ✅ Get distributor_id safely
    $stmt = $conn->prepare("
        SELECT f.distributor_id 
        FROM order_items oi
        JOIN food f ON oi.food_id = f.id
        WHERE oi.order_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("No distributor found");
    }

    $row = $result->fetch_assoc();
    $distributor_id = $row['distributor_id'];

    // ✅ Insert rating
    $stmt2 = $conn->prepare("
        INSERT INTO ratings (order_id, distributor_id, ngo_id, rating, review) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt2->bind_param("iiiis", $order_id, $distributor_id, $ngo_id, $rating, $review);

    if ($stmt2->execute()) {
        echo "✅ Rating submitted successfully!";
    } else {
        echo "Error: " . $stmt2->error;
    }
}
?>