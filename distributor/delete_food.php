<?php
include '../includes/auth.php';
include '../config/db.php';

isDistributor();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user = currentUser();

    // Ensure distributor can only delete their own food
    $conn->query("DELETE FROM food WHERE id='$id' AND distributor_id='{$user['id']}'");

    header("Location: view_food.php");
    exit();
}
?>