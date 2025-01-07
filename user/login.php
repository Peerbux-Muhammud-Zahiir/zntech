<?php
include('../includes/db_connect.php');
include('../functions/common_functions.php');
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php'); // Redirect to homepage if already logged in
    exit;
}




// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the form
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];

    // Validate input fields
    if (empty($user_email) || empty($user_password)) {
        $error_message = "Both fields are required.";
    } else {
        // Check if the email exists in the database
        $sql = "SELECT * FROM `user` WHERE `user_email` = :user_email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the user details
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($user_password, $user['user_password'])) {
                // Start the session and store user info
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_email'] = $user['user_email'];

                // Redirect to the dashboard or home page
                header('Location: ../index.php');
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
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
</head>
<body>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <!-- Show error messages -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <!-- Login form -->
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="user_email">Email Address</label>
                                <input type="email" id="user_email" name="user_email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="user_password">Password</label>
                                <input type="password" id="user_password" name="user_password" class="form-control" required>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>

                        <p class="text-center">Don't have an account? <a href="./registration.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
