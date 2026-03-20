<?php
include '../includes/auth.php';
include '../config/db.php';

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

    $success = "Food Posted Successfully!";
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Post Food</h2>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input name="food_name" class="form-control mb-2" placeholder="Food Name" required>
        <input name="plates" type="number" class="form-control mb-2" placeholder="Number of Plates" required>
        <input name="price" type="number" class="form-control mb-2" placeholder="Price" required>
        <input name="location" class="form-control mb-2" placeholder="Location" required>
        <input name="expiry" type="datetime-local" class="form-control mb-2" required>
        <textarea name="address" class="form-control mb-2" placeholder="Address"></textarea>
        <input name="pincode" class="form-control mb-2" placeholder="Pincode">

        <button class="btn btn-primary w-100">Post Food</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>