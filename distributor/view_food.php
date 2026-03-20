<?php
include '../includes/auth.php';
include '../config/db.php';

isDistributor();

$user = currentUser();

// Auto-remove expired food
$conn->query("UPDATE food SET status='sold' WHERE expiry < NOW()");

// ✅ UPDATED QUERY (with ratings)
$result = $conn->query("
    SELECT f.*, AVG(r.rating) as avg_rating
    FROM food f
    LEFT JOIN order_items oi ON oi.food_id = f.id
    LEFT JOIN ratings r ON r.order_id = oi.order_id
    WHERE f.distributor_id = '{$user['id']}'
    GROUP BY f.id
    ORDER BY f.id DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>My Food Listings</h2>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Food</th>
                <th>Plates</th>
                <th>Price</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Rating</th> <!-- ⭐ NEW -->
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['food_name']; ?></td>
                    <td><?php echo $row['plates']; ?></td>
                    <td>₹<?php echo $row['price']; ?></td>
                    <td><?php echo $row['expiry']; ?></td>

                    <td>
                        <?php if($row['status'] == 'available'): ?>
                            <span class="badge bg-success">Available</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Sold/Expired</span>
                        <?php endif; ?>
                    </td>

                    <!-- ⭐ SHOW RATING -->
                    <td>
                        <?php if($row['avg_rating']): ?>
                            ⭐ <?php echo round($row['avg_rating'],1); ?>/5
                        <?php else: ?>
                            <span class="text-muted">No Ratings</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($row['status'] == 'available'): ?>
                            <a href="delete_food.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this item?')">
                               Delete
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>