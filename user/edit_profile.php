<?php
include('../includes/db_connect.php'); // Database connection
include('../functions/common_functions.php'); // Common functions
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}

// Fetch user data from the database

$user_id = $_SESSION['user_id'];
function fetchUserData($pdo, $user_id){
    $sql = "SELECT * FROM `user` WHERE `user_id` = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
$user=fetchUserData($pdo,$user_id);
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];

    // Validate form inputs
    $errors = [];
    if (empty($user_name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match("/^5[0-9]{7}$/", $user_phone)) {
        $errors[] = "Invalid phone number. It must be 8 digits and start with 5.";
    }

    // Update user details if no errors
    if (empty($errors)) {
        $update_sql = "UPDATE `user` SET `user_name` = :user_name, `user_email` = :user_email, `user_phone` = :user_phone WHERE `user_id` = :user_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':user_name', $user_name);
        $update_stmt->bindParam(':user_email', $user_email);
        $update_stmt->bindParam(':user_phone', $user_phone);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $_SESSION['user_name'] = $user_name; // Update session with new name
            $success_message = "Profile updated successfully.";
            $user=fetchUserData($pdo,$user_id);
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Custom Styles -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=SP0rgjdOWCLf&format=png&color=000000">
</head>
<body>
    <?php include('../includes/header.php'); ?> <!-- Include header -->

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Edit Profile</h4>
                    </div>

        <!-- Display success or error messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST">
            <div class="card-body">
                <div class="form-group">
                    <label for="user_name" class="form-label">Full Name</label>
                    <input type="text" id="user_name" name="user_name" class="form-control" value="<?= htmlspecialchars($user['user_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="user_email" class="form-label">Email Address</label>
                    <input type="email" id="user_email" name="user_email" class="form-control" value="<?= htmlspecialchars($user['user_email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="user_phone" class="form-label">Phone Number</label>
                    <input type="number" id="user_phone" name="user_phone" class="form-control" value="<?= htmlspecialchars($user['user_phone']); ?>" required>
                </div>
                <div class="text-center">
                
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                <button type="reset" class="btn btn-secondary"><i class="fa fa-undo"></i> Reset</button>
                </div>
            </div>

        </form>
    </div>

    <?php include('../includes/footer.php'); ?> <!-- Include footer -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
