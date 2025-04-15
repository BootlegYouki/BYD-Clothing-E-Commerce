<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyond Doubt Clothing</title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">

    <!-- ICONS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>

    <!-- HEADER & MODALS -->
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/register.php'; ?>
    <?php include 'includes/login.php'; ?>
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <?php include 'includes/terms.php'; ?>
    <?php include 'includes/shopcart.php'; ?>

    <!-- SIDEBAR & MAIN -->
    <section class="d-flex mt-5 flex-wrap">
        <!-- Sidebar -->
        <div class="sidebar bg-light p-3 shadow-sm mt-5" style="width: 250px; min-height: 100vh;">
            <h2 class="d-flex align-items-center justify-content-between">
                <a class="text-dark text-decoration-none d-flex align-items-center w-100" data-bs-toggle="collapse" href="#accountMenu" role="button" aria-expanded="false">
                    <i class="fas fa-user-circle me-2"></i> <span>My Account</span>
                    <i class="fa fa-chevron-down ms-auto"></i>
                </a>
            </h2>

            <!-- Sidebar Buttons -->
            <div class="collapse show" id="accountMenu">
                <div class="d-flex flex-column">
                    <button class="btn sidebar-btn active" data-page="includes/profile_user_info.php">Profile</button>
                    <button class="btn sidebar-btn" data-page="includes/profile_address.php">Address</button>
                    <button class="btn sidebar-btn" data-page="includes/profile_changePass.php">Change Password</button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content flex-grow-1 p-4 mt-5">
            <!-- Dynamic content will be loaded here -->
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/indexscript.js"></script>
    <script src="js/profile.js"></script>

    <!-- Load default content on page load -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const defaultBtn = document.querySelector(".sidebar-btn.active");
            if (defaultBtn) defaultBtn.click();
        });
    </script>
</body>
</html>
