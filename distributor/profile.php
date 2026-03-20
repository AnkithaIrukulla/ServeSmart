<?php
require_once 'includes/auth.php';
checkAuth();

$user = $_SESSION['user'];
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
<h3>👤 Profile</h3>

<p><b>Name:</b> <?php echo $user['name']; ?></p>
<p><b>Email:</b> <?php echo $user['email']; ?></p>
<p><b>Role:</b> <?php echo $user['role']; ?></p>

<a href="settings.php" class="btn btn-warning">Edit Profile</a>
</div>

