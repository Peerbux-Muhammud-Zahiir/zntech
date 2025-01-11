<?php
// Start a session only if one doesn't exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('BASE_URL', 'http://localhost/zntech/');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZN Tech</title>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/styles.css">
    <link rel="icon" href="https://icons8.com/icon/F6ULPz8GgDMP/cube">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sevillana&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <!-- Logo and Home Link -->
        <a class="navbar-brand" href="<?= BASE_URL; ?>index.php">
            <i class="fa fa-cube" style="color:#BF40BF;"></i><span style="color: #BF40BF;font-weight:bold;text-decoration: none;"> ZN Tech</span>
        </a>
        
        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <!-- Products
                  -->
                <li class="nav-item">
                    <a href="<?= BASE_URL; ?>products.php">
                        <i class="fa fa-box-open"></i> 
                        Products
                        <!-- <button class="btn btn-primary"><i class="fa fa-box-open"></i> Products</button> -->
                    </a>
                <li class="nav-item">
                    <a href="<?= BASE_URL; ?>cart.php"><i class="fa fa-shopping-cart ml-3"></i> Cart</a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL; ?>/user/purchase_history.php"><i class="fa fa-list ml-3"></i> Purchase History</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <li class="">
                    <a href="<?= BASE_URL; ?>user/profile.php"><i class="fa fa-user ml-3"></i> Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></a>
                    </li>
                    <li class="">
                    <a href="<?= BASE_URL; ?>user/logout.php"><i class="fa fa-sign-out-alt ml-3 text-danger"></i><span class="text-danger"> Logout</span></a>
                    </li>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <li class="">
                        <a  href="<?= BASE_URL; ?>user/registration.php"><i class="fa fa-user-plus ml-3"></i> Register</a>
                    </li>
                    <li class="">
                        <a  href="<?= BASE_URL; ?>user/login.php"><i class="fa fa-sign-in-alt ml-3"></i> Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
