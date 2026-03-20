<?php
include '../includes/auth.php';
include '../config/db.php';

isNGO();

// Remove expired food
$conn->query("UPDATE food SET status='sold' WHERE expiry < NOW()");

$result = $conn->query("SELECT * FROM food WHERE status='available' ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Available Food</h2>

    <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <h5><?php echo $row['food_name']; ?></h5>
                    <p>Plates: <?php echo $row['plates']; ?></p>
                    <p>Price: ₹<?php echo $row['price']; ?></p>
                    <p>Expiry: <?php echo $row['expiry']; ?></p>
                    <p>Location: <?php echo $row['location']; ?></p>

                    <button class="btn btn-success"
                        onclick="addToCart(<?php echo $row['id']; ?>)">
                        Add to Cart
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addToCart(id){
    $.post('../api/add_to_cart.php', {id:id}, function(){
        alert("Added to Cart");
    });
}
</script>

<?php include '../includes/footer.php'; ?>