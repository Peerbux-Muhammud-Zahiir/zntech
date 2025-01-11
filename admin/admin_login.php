<?php
// Include the database connection and common functions
include('../includes/db_connect.php');
include('../functions/common_functions.php');

// Start a session
session_start();

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php'); // Redirect to the admin dashboard if already logged in
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the admin input from the form
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];

    // Validate input fields
    if (empty($admin_email) || empty($admin_password)) {
        $error_message = "Both fields are required.";
    } else {
        // Check if the email exists in the database
        $sql = "SELECT * FROM `admin` WHERE `admin_email` = :admin_email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':admin_email', $admin_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the admin details
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($admin_password, $admin['admin_password'])) {
                // Start the session and store admin info
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['admin_email'] = $admin['admin_email'];

                // Redirect to the admin dashboard
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
</head>
<body style="background-image:url('../assets/images/bg-image.webp')" >

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                  <div class="card-header bg-light text-dark">
                        <h4 class="text-center">Admin Login</h4>
                    </div>

                    <div class="card-body">
                        <!-- Show error messages -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <!-- Admin login form -->
                        <form action="admin_login.php" method="POST">
                            <div class="form-group">
                                <label for="admin_email">Email Address</label>
                                <input type="email" id="admin_email" name="admin_email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_password">Password</label>
                                <input type="password" id="admin_password" name="admin_password" class="form-control" required>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Login</button>
                            </div>
                        </form>

                        <p class="text-center">Don't have an account? <a href="admin_registration.php"><button class="btn btn-success btn-sm">Sign Up</button></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
