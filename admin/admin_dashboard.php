<?php
// Include the database connection and common functions
include('../includes/db_connect.php');
include('../functions/common_functions.php');

// Start the session
session_start();

// Check if the admin is logged in, if not redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Retrieve admin details from the session
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];

// Example: Fetching some admin data like total users and total orders
$sql_users = "SELECT COUNT(*) AS total_users FROM `user`";
$sql_orders = "SELECT COUNT(*) AS total_orders FROM `order`";
$sql_products = "SELECT COUNT(*) AS total_products FROM `product`";
$stmt_users = $pdo->query($sql_users);
$stmt_orders = $pdo->query($sql_orders);
$stmt_products = $pdo->query($sql_products);
$total_users = $stmt_users->fetch(PDO::FETCH_ASSOC)['total_users'];
$total_orders = $stmt_orders->fetch(PDO::FETCH_ASSOC)['total_orders'];
$total_products = $stmt_products->fetch(PDO::FETCH_ASSOC)['total_products'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- <link rel="stylesheet" href="../assets/css/bootstrap.css"> Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
</head>
<body>
<div class="container">
            <a href="admin_logout.php" class="btn btn-danger float-right"><span><i class="fa fa-sign-out-alt"></i></span> Logout</a>
        </div>
    <div class="container mt-5">
        <h1 class="text-center">Welcome, <?= htmlspecialchars($admin_name); ?>!</h1>

        <!-- Admin Dashboard Stats -->
        <div class="row">
        <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Total Products</h4>
                    </div>
                    <div class="card-body text-center">
                        <h3><?= $total_products; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Total Users</h4>
                    </div>
                    <div class="card-body text-center">
                        <h3><?= $total_users; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Total Orders</h4>
                    </div>
                    <div class="card-body text-center">
                        <h3><?= $total_orders; ?></h3>
                    </div>
                </div>
            </div>
           
        </div>

        <!-- Quick Links -->
        <div class="row mt-5 text-center"> 
            <div class="col-md-6">
                <a href="manage_users.php" class="btn btn-primary"><i class="fa fa-user"></i> Manage Users</a>
            </div>
            <div class="col-md-6">
                <a href="manage_orders.php" class="btn btn-primary"><i class="fa fa-box-open"></i> Manage Orders</a>
            </div>
            <div class="col-md-6 mt-3">
                <a href="manage_products.php" class="btn btn-primary"><i class="fa fa-box"></i> Manage Products</a>
            </div>
            <div class="col-md-6 mt-3">
                <a href="manage_payments.php" class="btn btn-primary"><i class="fa fa-credit-card"></i> Manage Payment</a>
            </div>
            <div class="col-md-6 mt-3">
                <a href="manage_reviews.php" class="btn btn-primary"><i class="fa fa-star"></i> Manage Reviews</a>
            </div>

        <!-- Logout -->
     
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
