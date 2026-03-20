<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

checkAuth();

$user = $_SESSION['user'];

if($_POST){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Get current password from DB
    $result = $conn->query("SELECT password FROM users WHERE id='{$user['id']}'");
    $row = $result->fetch_assoc();
    $current_hashed = $row['password'];

    if(!empty($password)){

        // ❌ Password mismatch
        if($password !== $confirm_password){
            $error = "⚠️ New password and confirm password must match!";
        }

        // ❌ Same as old password
        elseif(password_verify($password, $current_hashed)){
            $error = "⚠️ New password cannot be same as old password!";
        }

        // ✅ Valid
        else{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $conn->query("UPDATE users 
                          SET name='$name', email='$email', password='$hashed_password' 
                          WHERE id='{$user['id']}'");

            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;

            $msg = "✅ Profile updated successfully!";
        }
    } 
    else{
        $conn->query("UPDATE users 
                      SET name='$name', email='$email' 
                      WHERE id='{$user['id']}'");

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;

        $msg = "✅ Profile updated successfully!";
    }
}

?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
<h3>⚙️ Settings</h3>

<?php if(isset($error)): ?>
<div class="alert alert-danger">
    <?php echo $error; ?>
</div>
<?php endif; ?>

<?php if(isset($msg)): ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php endif; ?>

<form method="POST">
<input type="text" name="name" class="form-control mb-2" value="<?php echo $user['name']; ?>" placeholder="Name">
<input type="email" name="email" class="form-control mb-2" value="<?php echo $user['email']; ?>" placeholder="E-mail">
<input type="password" name="password" class="form-control mb-2" placeholder="New Password">
<input type="password" name="confirm_password" class="form-control mb-2" placeholder="Confirm Password">
<button class="btn btn-primary">Update</button>
</form>
</div>

