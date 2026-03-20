<?php
include '../includes/auth.php';
include '../config/db.php';

isNGO();

$user = currentUser();
$cart = $_SESSION['cart'] ?? [];

$total = 0;

foreach ($cart as $id) {
    $res = $conn->query("SELECT * FROM food WHERE id=$id");
    $row = $res->fetch_assoc();
    $total += $row['price'];
}

if ($_POST && !empty($cart)) {

    // Create order
    $conn->query("INSERT INTO orders(ngo_id,total) VALUES('{$user['id']}','$total')");
    $order_id = $conn->insert_id;

    foreach ($cart as $id) {
        $conn->query("INSERT INTO order_items(order_id,food_id,quantity)
                      VALUES('$order_id','$id',1)");

        // Mark food as sold
        $conn->query("UPDATE food SET status='sold' WHERE id=$id");
    }

    unset($_SESSION['cart']);

    $success = "Order placed successfully!";
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Checkout</h2>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <a href="orders.php" class="btn btn-success">View Orders</a>
    <?php elseif(empty($cart)): ?>
        <p>No items to checkout.</p>
    <?php else: ?>

    <h4>Total Amount: ₹<?php echo $total; ?></h4>

    <form method="POST">
        <select name="payment" class="form-control mb-3">
            <option value="COD">Cash on Delivery</option>
            <option value="UPI">UPI</option>
        </select>

        <button class="btn btn-primary w-100">Place Order</button>
    </form>

    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>