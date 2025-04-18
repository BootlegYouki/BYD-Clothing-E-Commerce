<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

// Get admin user information from session
$user_id = $_SESSION['auth_user']['user_id'] ?? 1;
$name = $_SESSION['auth_user']['name'] ?? 'Admin User';
$email = $_SESSION['auth_user']['email'] ?? 'admin@example.com';
$phone = $_SESSION['auth_user']['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Admin | Beyond Doubt Clothing</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <?php if(isset($_SESSION['message'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
            <strong>Success!</strong> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-alert').classList.remove('show');
                setTimeout(function() {
                    document.getElementById('success-alert')?.remove();
                }, 150);
            }, 3000);
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card">
                <div class="card-header p-0 position-relative mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-black mb-0">Profile Information</h5>
                            <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                <i class="material-symbols-rounded opacity-10">person</i>
                            </div>
                        </div>
                        <p class="text-b text-xs mb-0 opacity-8">Manage your personal information</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar rounded-circle bg-light mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="material-symbols-rounded" style="font-size: 48px;">person</i>
                        </div>
                        <h5 class="mt-3 mb-0"><?= htmlspecialchars($name) ?></h5>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                    
                    <div class="info-item d-flex align-items-center mb-3">
                        <div class="icon bg-gradient-primary text-white me-3 d-flex align-items-center justify-content-center" style="min-width: 40px; height: 40px; border-radius: 8px;">
                            <i class="material-symbols-rounded">email</i>
                        </div>
                        <div>
                            <p class="text-xs text-muted mb-0">Email</p>
                            <h6 class="mb-0"><?= htmlspecialchars($email) ?></h6>
                        </div>
                    </div>
                    
                    <div class="info-item d-flex align-items-center mb-3">
                        <div class="icon bg-gradient-primary text-white me-3 d-flex align-items-center justify-content-center" style="min-width: 40px; height: 40px; border-radius: 8px;">
                            <i class="material-symbols-rounded">phone</i>
                        </div>
                        <div>
                            <p class="text-xs text-muted mb-0">Phone</p>
                            <h6 class="mb-0"><?= $phone ? htmlspecialchars($phone) : 'Not provided' ?></h6>
                        </div>
                    </div>
                    
                    <div class="info-item d-flex align-items-center">
                        <div class="icon bg-gradient-primary text-white me-3 d-flex align-items-center justify-content-center" style="min-width: 40px; height: 40px; border-radius: 8px;">
                            <i class="material-symbols-rounded">shield_person</i>
                        </div>
                        <div>
                            <p class="text-xs text-muted mb-0">Role</p>
                            <h6 class="mb-0">Administrator</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Profile Card -->
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card">
                <div class="card-header p-0 position-relative mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-black mb-0">Update Profile</h5>
                            <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                <i class="material-symbols-rounded opacity-10">edit</i>
                            </div>
                        </div>
                        <p class="text-b text-xs mb-0 opacity-8">Update your personal information</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="#" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($phone) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" class="form-control" name="profile_image">
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(195deg, #FF7F50, #FF6347);">
                                    <i class="material-symbols-rounded me-1" style="vertical-align: middle;">save</i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings Card -->
            <div class="card mt-4">
                <div class="card-header p-0 position-relative mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-black mb-0">Security Settings</h5>
                            <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                <i class="material-symbols-rounded opacity-10">lock</i>
                            </div>
                        </div>
                        <p class="text-b text-xs mb-0 opacity-8">Manage your security settings</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="#" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="material-symbols-rounded me-2">info</i>
                                    <div>
                                        Password must be at least 8 characters long and include uppercase letters, lowercase letters, numbers, and special characters.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn" style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white;">
                                    <i class="material-symbols-rounded me-1" style="vertical-align: middle;">key</i>
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>