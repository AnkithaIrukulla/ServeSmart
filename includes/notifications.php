<?php
require_once __DIR__ . '/../config/db.php';

function addNotification($user_id, $message){
    global $conn;
    $conn->query("INSERT INTO notifications (user_id,message) VALUES ('$user_id','$message')");
}

/* =========================
   ⏰ CHECK EXPIRY (15 MIN)
========================= */
function checkExpiryNotifications(){
    global $conn;

    $result = $conn->query("
        SELECT f.food_name, o.ngo_id
        FROM food f
        JOIN order_items oi ON oi.food_id = f.id
        JOIN orders o ON o.id = oi.order_id
        WHERE f.expiry BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 15 MINUTE)
    ");

    // ❗ FIX 1: check query success
    if(!$result){
        return; // stop if query fails
    }

    while($row = $result->fetch_assoc()){
        $message = "⚠️ Food '{$row['food_name']}' is expiring in 15 minutes!";

        $check = $conn->query("
            SELECT id FROM notifications 
            WHERE user_id='{$row['ngo_id']}' 
            AND message='$message'
        ");

        // ❗ FIX 2: check before using num_rows
        if($check && $check->num_rows == 0){
            addNotification($row['ngo_id'], $message);
        }
    }
}
?>