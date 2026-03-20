<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
checkAuth();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
<h3>Available Food</h3>

<a href="cart.php" class="btn btn-dark mb-3">🛒 Cart</a>

<?php
$res = $conn->query("SELECT * FROM food WHERE expiry > NOW()");
while($row = $res->fetch_assoc()):
?>

<div class="card p-3 mb-2">
<h5><?php echo $row['food_name']; ?></h5>

<button class="btn btn-success addToCart" data-id="<?php echo $row['id']; ?>">
Add to Cart
</button>
</div>

<?php endwhile; ?>
</div>

<script>
$(".addToCart").click(function(){
    let id = $(this).data("id");

    $.post("<?php echo BASE_URL; ?>api/add_to_cart.php",
    {food_id:id},
    function(res){
        alert(res);
    });
});
</script>

<?php include '../includes/footer.php'; ?>