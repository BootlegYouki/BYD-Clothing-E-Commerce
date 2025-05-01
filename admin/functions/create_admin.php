<?php
session_start();
include '../config/dbcon.php';

// Process admin creation
if (isset($_POST['create_admin'])) {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: create_admin.php");
        exit;
    }
    
    // Check if username already exists
    $check_username_query = "SELECT username FROM users WHERE username='$username'";
    $check_username_query_run = mysqli_query($conn, $check_username_query);
    
    if (mysqli_num_rows($check_username_query_run) > 0) {
        $_SESSION['error_message'] = "Username already exists. Please choose a different username.";
        header("Location: create_admin.php");
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Create admin user (role_as = 1 indicates admin status)
    // Using placeholder values for required fields
    $query = "INSERT INTO users 
             (firstname, lastname, phone_number, email, username, password, role_as, email_verified, created_at) 
             VALUES 
             ('Admin', 'User', '0000000000', '$username@admin.com', '$username', '$hashedPassword', 1, 1, NOW())";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Admin user created successfully!";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Error creating admin user: " . mysqli_error($conn);
        header("Location: create_admin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Create Admin User</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error_message']; ?>
                                <?php unset($_SESSION['error_message']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" name="create_admin" class="btn btn-primary">Create Admin User</button>
                                <a href="../index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
