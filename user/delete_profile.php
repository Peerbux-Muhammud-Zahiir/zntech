<?php
include('../includes/db_connect.php'); // Include database connection
include('../functions/common_functions.php'); // Include common functions
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    if ($_POST['confirm_delete'] === 'yes') {
        $sql = "DELETE FROM `user` WHERE `user_id` = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Destroy the session after account deletion
            session_destroy();
            header('Location: login.php'); // Redirect to a goodbye page or home page
            exit;
        } else {
            $error_message = "Failed to delete account. Please try again.";
        }
    } else {
        // User canceled deletion
        header('Location: profile.php'); // Redirect to profile page
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
    <link rel="stylesheet" href="../assets/css/bootstrap.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Custom Styles -->
</head>
<body>
    <?php include('../includes/header.php'); ?> <!-- Include header -->

    <div class="container mt-5">
        <h2 class="text-danger mb-4">Delete Account</h2>
        <p class="text-warning">Are you sure you want to delete your account? This action cannot be undone.</p>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="delete_profile.php" method="POST">
            <div class="mb-3">
                <label for="confirm_delete" class="form-label">Type "yes" to confirm:</label>
                <input type="text" id="confirm_delete" name="confirm_delete" class="form-control" placeholder="yes or no" required>
            </div>

            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete My Account</button>
            <a href="profile.php" class="btn btn-secondary"><i class="fa fa-times"></i> Cancel</a>
        </form>
    </div>

    <?php include('../includes/footer.php'); ?> <!-- Include footer -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
