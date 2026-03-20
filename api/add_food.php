<?php
include '../config/db.php';
include '../includes/auth.php';

isDistributor();
$user = currentUser();

if ($_POST) {

    $food_name = $_POST['food_name'];
    $plates = $_POST['plates'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $expiry = $_POST['expiry'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];

    $conn->query("INSERT INTO food(distributor_id,food_name,plates,price,location,expiry,address,pincode)
                  VALUES('{$user['id']}','$food_name','$plates','$price','$location','$expiry','$address','$pincode')");

    echo json_encode([
        "status" => "success",
        "message" => "Food added successfully"
    ]);
}
?>