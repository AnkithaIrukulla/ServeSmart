<?php
include 'includes/auth.php';
checkAuth();

$user = currentUser();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Welcome, <?php echo $user['name']; ?> 👋</h2>

    <?php if($user['role'] == 'distributor'): ?>

        <div class="card p-3 mt-3">
            <h4>Distributor Panel</h4>
            <a href="distributor/post_food.php" class="btn btn-primary mt-2">Post Food</a>
            <a href="distributor/view_food.php" class="btn btn-secondary mt-2">My Listings</a>
        </div>

    <?php else: ?>

        <div class="card p-3 mt-3">
            <h4>NGO Panel</h4>
            <a href="ngo/browse_food.php" class="btn btn-success mt-2">Browse Food</a>
            <a href="ngo/cart.php" class="btn btn-warning mt-2">View Cart</a>
            <a href="ngo/orders.php" class="btn btn-info mt-2">My Orders</a>
        </div>

    <?php endif; ?>
</div>

