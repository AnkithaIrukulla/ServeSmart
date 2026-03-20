<?php
include '../includes/auth.php';
include '../config/db.php';

isNGO();

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Your Cart</h2>

    <?php if(empty($cart)): ?>
        <p>No items in cart.</p>
    <?php else: ?>

    <table class="table">
        <tr>
            <th>Food</th>
            <th>Price</th>
        </tr>

        <?php foreach($cart as $id): 
            $res = $conn->query("SELECT * FROM food WHERE id=$id");
            $row = $res->fetch_assoc();
            $total += $row['price'];
        ?>
        <tr>
            <td><?php echo $row['food_name']; ?></td>
            <td>₹<?php echo $row['price']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h4>Total: ₹<?php echo $total; ?></h4>

    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>

    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>