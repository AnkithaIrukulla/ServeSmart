<?php
include 'config/db.php';

if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $location = $_POST['location'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];

    // Check duplicate email
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $conn->query("INSERT INTO users(name,email,password,role,location,address,pincode)
                      VALUES('$name','$email','$pass','$role','$location','$address','$pincode')");
        $success = "Registered Successfully!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Register</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input name="name" class="form-control mb-2" placeholder="Name" required>
        <input name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

        <select name="role" class="form-control mb-2">
            <option value="distributor">Distributor</option>
            <option value="ngo">NGO</option>
        </select>

        <input name="location" class="form-control mb-2" placeholder="Location">
        <textarea name="address" class="form-control mb-2" placeholder="Address"></textarea>
        <input name="pincode" class="form-control mb-2" placeholder="Pincode">

        <button class="btn btn-primary w-100">Register</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>