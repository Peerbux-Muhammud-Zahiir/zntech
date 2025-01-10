<?php
// Include the database connection and common functions
include('../includes/db_connect.php');
include('../functions/common_functions.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the form
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];
    $user_password = $_POST['user_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input fields
    if (empty($user_name) || empty($user_email) || empty($user_phone) || empty($user_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($user_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^5[0-9]{7}$/", $user_phone)) {
        $error_message = "Invalid phone number. It must be 8 digits.";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM `user` WHERE `user_email` = :user_email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "Email is already registered.";
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql_insert = "INSERT INTO `user` (user_name, user_email, user_phone, user_password) 
                           VALUES (:user_name, :user_email, :user_phone, :user_password)";
            $stmt = $pdo->prepare($sql_insert);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_email', $user_email);
            $stmt->bindParam(':user_phone', $user_phone);
            $stmt->bindParam(':user_password', $hashed_password);
            $stmt->execute();

            // Success message
            $success_message = "Registration successful! Please log in.";
            header("Location: login.php");
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
    <title>User Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
</head>
<body class="bg-light" style="background-image: url('../assets/images/bg-image.webp');">

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>User Registration</h3>
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
                        <form action="registration.php" method="POST">
                            <div class="form-group">
                                <label for="user_name">Full Name</label>
                                <input type="text" id="user_name" name="user_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="user_email">Email Address</label>
                                <input type="email" id="user_email" name="user_email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="user_phone">Phone Number</label>
                                <input type="text" id="user_phone" name="user_phone" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="user_password">Password</label>
                                <input type="password" id="user_password" name="user_password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success btn-sm">Sign Up</button>
                            </div>
                        </form>

                        <p class="text-center">Already have an account? <a href="./login.php"><button class="btn btn-primary btn-sm">Log In</button></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
