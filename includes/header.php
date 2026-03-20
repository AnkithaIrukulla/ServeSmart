<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/notifications.php';

// Run expiry check only if user logged in
if(isset($_SESSION['user'])){
    checkExpiryNotifications();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ServeSmart</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<!-- 🌿 Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" 
     style="background: linear-gradient(135deg, #56CCF2, #6FCF97);">

    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="/ServeSmart/index.php">
            🍱 ServeSmart
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-center">

                <?php if(isset($_SESSION['user'])): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/ServeSmart/dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <?php if($_SESSION['user']['role'] == 'distributor'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ServeSmart/distributor/post_food.php">
                                <i class="bi bi-plus-circle"></i> Post Food
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ServeSmart/ngo/browse_food.php">
                                <i class="bi bi-search"></i> Browse
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ServeSmart/ngo/cart.php">
                                <i class="bi bi-cart"></i> Cart
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- 🔔 Notifications -->
                    <?php
                    require_once __DIR__ . '/../config/db.php';

                    $user_id = $_SESSION['user']['id'];

                    // Get unread count
                    $noti = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE user_id='$user_id' AND is_read=0");
                    $n = $noti->fetch_assoc();

                    // Get latest notifications
                    $list = $conn->query("SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY id DESC LIMIT 5");

                    // Mark as read
                    $conn->query("UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");
                    ?>

                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            🔔
                            <?php if($n['total'] > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                    <?php echo $n['total']; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Dropdown -->
                        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px; max-height:300px; overflow-y:auto;">
                            
                            <?php if($list->num_rows > 0): ?>
                                <?php while($row = $list->fetch_assoc()): ?>
                                    <li class="mb-2 border-bottom pb-2">
                                        <small><?php echo $row['message']; ?></small><br>
                                        <small class="text-muted"><?php echo $row['created_at']; ?></small>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="text-center text-muted">No notifications</li>
                            <?php endif; ?>

                        </ul>
                    </li>

                    <!-- User Name -->
                    <li class="nav-item">
                        <span class="nav-link fw-semibold">
                            👤 
                            <a href="/ServeSmart/profile.php" class="text-white text-decoration-none">
                                <?php echo $_SESSION['user']['name']; ?>
                            </a>
                        </span>
                    </li>

                    <!-- Logout -->
                    <li class="nav-item ms-2">
                        <a class="btn btn-light btn-sm rounded-pill px-3" href="/ServeSmart/logout.php">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/ServeSmart/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>

                    <li class="nav-item ms-2">
                        <a class="btn btn-light btn-sm rounded-pill px-3" href="/ServeSmart/register.php">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>

<!-- Page Content -->
<div class="container mt-4">