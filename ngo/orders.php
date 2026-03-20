<?php
include '../includes/auth.php';
include '../config/db.php';

isNGO();

$user = currentUser();

/* =========================
   ⭐ SUBMIT RATING
========================= */
if(isset($_POST['submit_rating'])){
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $ngo_id = $user['id'];

    // Get distributor_id
    $res = $conn->query("
        SELECT f.distributor_id 
        FROM order_items oi
        JOIN food f ON oi.food_id = f.id
        WHERE oi.order_id = '$order_id'
        LIMIT 1
    ");
    $data = $res->fetch_assoc();
    $distributor_id = $data['distributor_id'];

    // Prevent duplicate rating
    $check = $conn->query("SELECT * FROM ratings WHERE order_id='$order_id'");
    if($check->num_rows == 0){
        $conn->query("
            INSERT INTO ratings (order_id, distributor_id, ngo_id, rating)
            VALUES ('$order_id', '$distributor_id', '$ngo_id', '$rating')
        ");
    }
}

/* =========================
   📦 FETCH ORDERS WITH FOOD
========================= */
$result = $conn->query("
    SELECT o.*, f.food_name
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN food f ON f.id = oi.food_id
    WHERE o.ngo_id = '{$user['id']}'
    ORDER BY o.id DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>My Orders</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Food</th> <!-- ✅ changed -->
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Rating</th>
            </tr>
        </thead>

        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <!-- ✅ FOOD NAME -->
                    <td><?php echo $row['food_name']; ?></td>

                    <td>₹<?php echo $row['total']; ?></td>

                    <td>
                        <span class="badge bg-info">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>

                    <td><?php echo $row['created_at']; ?></td>

                    <!-- ⭐ RATING -->
                    <td>
                        <?php
                        $check = $conn->query("SELECT * FROM ratings WHERE order_id='{$row['id']}'");

                        if($check->num_rows > 0):
                            $r = $check->fetch_assoc();
                        ?>
                            ⭐ <?php echo $r['rating']; ?>/5

                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">

                                <select name="rating" class="form-control mb-1" required>
                                    <option value="">Rate</option>
                                    <option value="5">⭐ 5</option>
                                    <option value="4">⭐ 4</option>
                                    <option value="3">⭐ 3</option>
                                    <option value="2">⭐ 2</option>
                                    <option value="1">⭐ 1</option>
                                </select>

                                <button name="submit_rating" class="btn btn-sm btn-success">
                                    Submit
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>