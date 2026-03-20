<?php
session_start();
include 'config/db.php';

/* =========================
   🔐 RESET PASSWORD LOGIC
========================= */
if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error = "⚠️ Passwords do not match!";
    } else {
        // Check if email exists
        $res = $conn->query("SELECT * FROM users WHERE email='$email'");

        if ($res->num_rows > 0) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $conn->query("UPDATE users SET password='$hashed' WHERE email='$email'");
            $msg = "✅ Password reset successful! You can login now.";
        } else {
            $error = "⚠️ Email not found!";
        }
    }
}


/* =========================
   🔑 LOGIN LOGIC
========================= */
if (isset($_POST['email']) && !isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $res->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "⚠️ Invalid Email or Password!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>
        <?php echo isset($_GET['forgot']) ? "Reset Password" : "Login"; ?>
    </h2>

    <!-- Alerts -->
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>


    <?php if(isset($_GET['forgot'])): ?>

        <!-- 🔐 RESET PASSWORD FORM -->
        <form method="POST">
            <input name="email" class="form-control mb-2" placeholder="Enter your email" required>

            <input name="new_password" type="password" class="form-control mb-2" placeholder="New Password" required>

            <input name="confirm_password" type="password" class="form-control mb-2" placeholder="Confirm Password" required>

            <button name="reset_password" class="btn btn-warning w-100">Reset Password</button>

            <div class="text-center mt-3">
                <a href="login.php">Back to Login</a>
            </div>
        </form>

    <?php else: ?>

        <!-- 🔑 LOGIN FORM -->
        <form method="POST">
            <input name="email" class="form-control mb-2" placeholder="Email" required>

            <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>

            <div class="text-end mb-3">
                <a href="?forgot=1" style="color: orange; text-decoration: none;">
                    Forgot Password?
                </a>
            </div>

            <button class="btn btn-success w-100">Login</button>
        </form>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>