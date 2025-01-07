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

// Fetch all users from the database
$sql_users = "SELECT * FROM `user`";
$stmt_users = $pdo->query($sql_users);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Handle delete user request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);

    // Delete the user from the database
    $sql_delete = "DELETE FROM `user` WHERE `user_id` = :user_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':user_id', $user_id);
    $stmt_delete->execute();

    $_SESSION['success_message'] = "User has been deleted successfully.";
    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
</head>
<body>


    <div class="container mt-5">
        <h1 class="text-center">Manage Users</h1>

        <!-- Show success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Users Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']); ?></td>
                            <td><?= htmlspecialchars($user['user_name']); ?></td>
                            <td><?= htmlspecialchars($user['user_email']); ?></td>
                            <td><?= htmlspecialchars($user['user_phone']); ?></td>
                            <td>
                                <a href="view_user.php?id=<?= $user['user_id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit_user.php?id=<?= $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="manage_users.php?delete=<?= $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Back to Dashboard Button -->
        <div class="text-center mt-5">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
