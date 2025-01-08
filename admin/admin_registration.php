<?php
// Include the database connection and common functions
include('../includes/db_connect.php');
include('../functions/common_functions.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve admin input from the form
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input fields
    if (empty($admin_name) || empty($admin_email) || empty($admin_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($admin_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if email already exists in the admin table
        $sql = "SELECT * FROM `admin` WHERE `admin_email` = :admin_email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':admin_email', $admin_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "Email is already registered.";
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

            // Insert the new admin into the database
            $sql_insert = "INSERT INTO `admin` (admin_name, admin_email, admin_password) 
                           VALUES (:admin_name, :admin_email, :admin_password)";
            $stmt = $pdo->prepare($sql_insert);
            $stmt->bindParam(':admin_name', $admin_name);
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':admin_password', $hashed_password);
            $stmt->execute();

            // Success message
            $success_message = "Registration successful! Please log in.";
            header("Location: admin_login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=SP0rgjdOWCLf&format=png&color=000000">
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Admin Registration</h3>
                    </div>
                    <div class="card-body">
                        <!-- Show error or success messages -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>

                        <!-- Registration form -->
                        <form action="admin_registration.php" method="POST">
                            <div class="form-group">
                                <label for="admin_name">Full Name</label>
                                <input type="text" id="admin_name" name="admin_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_email">Email Address</label>
                                <input type="email" id="admin_email" name="admin_email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_password">Password</label>
                                <input type="password" id="admin_password" name="admin_password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>

                        <p class="text-center">Already have an account? <a href="admin_login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
