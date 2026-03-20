<?php
require_once '../config/db.php';

$search = $_POST['search'] ?? '';
$location = $_POST['location'] ?? '';
$min_price = $_POST['min_price'] ?? 0;
$max_price = $_POST['max_price'] ?? 999999;
$sort = $_POST['sort'] ?? '';

// ✅ Correct table + columns
$query = "SELECT * FROM food WHERE status='available'";

// 🔍 Intelligent search (food_name + location)
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (food_name LIKE '%$search%' OR location LIKE '%$search%')";
}

// 📍 Location filter
if (!empty($location)) {
    $location = $conn->real_escape_string($location);
    $query .= " AND location LIKE '%$location%'";
}

// 💰 Price filter
$min_price = (int)$min_price;
$max_price = (int)$max_price;
$query .= " AND price BETWEEN $min_price AND $max_price";

// 🧠 Smart Sorting
if ($sort == "fresh") {
    $query .= " ORDER BY expiry ASC"; // nearest expiry first
} elseif ($sort == "price_low") {
    $query .= " ORDER BY price ASC";
} elseif ($sort == "price_high") {
    $query .= " ORDER BY price DESC";
}

// ✅ Execute query
$result = $conn->query($query);

// ❌ Debug if error
if (!$result) {
    die("SQL Error: " . $conn->error);
}

// 🎯 Output
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // ⚠️ Expiry warning logic
        $expiry_time = strtotime($row['expiry']);
        $current_time = time();
        $hours_left = ($expiry_time - $current_time) / 3600;

        $warning = "";
        if ($hours_left < 5) {
            $warning = "<span style='color:red;'>⚠️ Expiring Soon</span>";
        }

        echo "<div style='border:1px solid #ddd;padding:15px;margin-bottom:10px;border-radius:10px;background:#fff;'>";

        echo "<h3>{$row['food_name']}</h3>";
        echo "<p>📍 Location: {$row['location']}</p>";
        echo "<p>🍽 Plates: {$row['plates']}</p>";
        echo "<p>💰 Price: ₹{$row['price']}</p>";
        echo "<p>⏳ Expiry: {$row['expiry']} $warning</p>";

        echo "<button style='padding:8px 15px;background:#28a745;color:#fff;border:none;border-radius:5px;'>
                Order Now
              </button>";

        echo "</div>";
    }
} else {
    echo "<p style='text-align:center;'>No food found 😔</p>";
}
?>