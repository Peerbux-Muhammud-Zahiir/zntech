<?php
include("../includes/db_connect.php");
include("../functions/common_functions.php");
if(session_status()===PHP_SESSION_NONE){
session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php"); // Redirect to login page if not logged in
    exit;
}


// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `user` WHERE `user_id` = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user_details = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link rel="stylesheet" href="../assets/css/styles.css"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
</head>
<body>
        <!-- Preloader -->
<div id="preloader">
    <div>
        <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-secondary" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-success" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-danger" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-warning" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-info" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-light" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-dark" role="status"><span class="sr-only">Loading...</span></div>
    </div>
</div>
    <?php include('../includes/header.php'); ?> <!-- Include header -->

    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="fa fa-user"></i> Profile</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        User Details
                    </div>
                    <div class="card-body">
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($user_details['user_name']); ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user_details['user_email']); ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user_details['user_phone']); ?></p>
                    </div>
                    <div class="card-footer text-center">
                        <a href="edit_profile.php" class="btn btn-warning"><i class="fa fa-edit"></i> Edit Profile</a>
                        <a href="delete_profile.php" class="btn btn-danger"><i class="fa fa-trash"></i> Delete Profile</a>
                        <!-- <a href="../user/logout.php" class="btn btn-danger"><i class="fa fa-sign-out-alt"></i> Logout</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?> <!-- Include footer -->

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
