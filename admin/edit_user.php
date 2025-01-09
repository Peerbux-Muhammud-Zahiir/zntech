<?php
include('../includes/db_connect.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Check if a user ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch existing user details
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<script>alert('User not found.'); window.location.href = 'manage_users.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'manage_users.php';</script>";
    exit;
}

// Handle form submission
if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate fields
    if (empty($username) || empty($email) || empty($phone)) {
        echo "<script>alert('Fields should not be empty.');</script>";
    } else {
        // Update user details in the database
        $sql="UPDATE user SET user_name = :username, user_email = :email,user_phone=:phone WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone',$phone);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully.'); window.location.href = 'manage_users.php';</script>";
        } else {
            echo "<script>alert('Error updating user.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome -->
</head>
<body class="bg-light">
<div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Edit User</h4>
                    </div>
        <form action="" method="post">
            <!-- Username -->
             <div class="card-body">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['user_name']) ?>" required>
            </div>
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['user_email']) ?>" required>
            </div>
            <!-- Phone -->
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['user_phone']) ?>" required>
</br>
                <!-- Submit Button -->
                 <div class="text-center">
                 <button type="submit" name="update_user" class="btn btn-success"><i class="fa fa-edit"></i> Update</button>
                    <a href="manage_users.php" class="btn btn-secondary"><i class="fa fa-xmark"></i> Cancel</a>
                 </div>
            
        </form>

    </div>
</div>
</div>
</div>

</body>
</html>
