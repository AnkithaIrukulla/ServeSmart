<?php
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'distributor') {
    header("Location: /ServeSmart/login.php");
    exit;
}

$distributor_id = $_SESSION['user']['id'];
?>

<div class="container mt-4">

    <h2 class="mb-4">👋 Welcome, <?php echo $_SESSION['user']['name']; ?></h2>

    <!-- 📊 DASHBOARD STATS -->
    <div class="row text-center mb-4">

        <?php
        // Total food posted
        $foodCount = $conn->query("SELECT COUNT(*) as total FROM food WHERE distributor_id = $distributor_id")->fetch_assoc()['total'];

        // Total orders
        $orderCount = $conn->query("
            SELECT COUNT(*) as total 
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN food f ON oi.food_id = f.id
            WHERE f.distributor_id = $distributor_id
        ")->fetch_assoc()['total'];

        // Average rating
        $ratingData = $conn->query("
            SELECT AVG(rating) as avg_rating 
            FROM ratings 
            WHERE distributor_id = $distributor_id
        ")->fetch_assoc();

        $avgRating = round($ratingData['avg_rating'], 1);
        ?>

        <div class="col-md-4">
            <div class="card shadow p-3">
                <h4>🍱 Food Posted</h4>
                <h2><?php echo $foodCount; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-3">
                <h4>📦 Orders Received</h4>
                <h2><?php echo $orderCount; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-3">
                <h4>⭐ Avg Rating</h4>
                <h2><?php echo $avgRating ? $avgRating : "0"; ?>/5</h2>
            </div>
        </div>

    </div>

    <!-- 🍽️ FOOD LIST -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            Your Food Listings
        </div>
        <div class="card-body">

            <?php
            $foods = $conn->query("SELECT * FROM food WHERE distributor_id = $distributor_id ORDER BY created_at DESC");

            if ($foods->num_rows > 0):
                while ($food = $foods->fetch_assoc()):
            ?>

            <div class="border p-3 mb-3 rounded">
                <h5><?php echo $food['food_name']; ?></h5>
                <p>🍽 Plates: <?php echo $food['plates']; ?></p>
                <p>💰 Price: ₹<?php echo $food['price']; ?></p>
                <p>📍 Location: <?php echo $food['location']; ?></p>
                <p>⏳ Expiry: <?php echo $food['expiry']; ?></p>
                <span class="badge bg-<?php echo ($food['status'] == 'available') ? 'success' : 'secondary'; ?>">
                    <?php echo $food['status']; ?>
                </span>
            </div>

            <?php endwhile; else: ?>
                <p>No food posted yet.</p>
            <?php endif; ?>

        </div>
    </div>

    <!-- ⭐ RATINGS SECTION -->
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark">
            ⭐ Your Ratings & Reviews
        </div>
        <div class="card-body">
            <div id="ratings">Loading ratings...</div>
        </div>
    </div>

</div>

<!-- 🔄 AJAX LOAD RATINGS -->
<script>
function loadRatings(distributor_id) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "/ServeSmart/api/fetch_ratings.php?distributor_id=" + distributor_id, true);

    xhr.onload = function () {
        document.getElementById("ratings").innerHTML = this.responseText;
    };

    xhr.send();
}

// Load ratings on page load
loadRatings(<?php echo $distributor_id; ?>);
</script>

<?php include '../includes/footer.php'; ?>