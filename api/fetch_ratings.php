<?php
include '../config/db.php';

$distributor_id = $_GET['distributor_id'];

$query = "SELECT r.rating, r.review, r.created_at, u.name AS ngo_name
          FROM ratings r
          JOIN users u ON r.ngo_id = u.id
          WHERE r.distributor_id = $distributor_id
          ORDER BY r.created_at DESC";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border:1px solid #ddd;padding:10px;margin:10px;border-radius:8px;'>";
        echo "<strong>" . $row['ngo_name'] . "</strong><br>";
        echo "⭐ Rating: " . $row['rating'] . "/5<br>";
        echo "<p>" . $row['review'] . "</p>";
        echo "<small>" . $row['created_at'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>No ratings yet</p>";
}
?>