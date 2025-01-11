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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center">Manage Users</h1>
        <!-- Back to Dashboard Button -->
        <div class="float-left mb-2">
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fa fa-home"></i> Dashboard</a>
        </div>

        <!-- Show success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Users Table -->
        <table class="table table-bordered">
            <thead class="thead-light">
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
                                <a href="edit_user.php?id=<?= $user['user_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <!-- Button to trigger the confirmation modal -->
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDeleteModal" data-delete-url="manage_users.php?delete=<?= $user['user_id']; ?>"><i class="fa fa-trash"></i> Delete</a>
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

    </div>

    <!-- Bootstrap Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer text-center">
                    <a href="manage_users.php?delete=<?= $user['user_id']; ?>" class="btn btn-danger">Delete</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to handle the modal functionality -->
    <script>
        // Attach event listener to dynamically set the delete URL
        document.addEventListener('DOMContentLoaded', () => {
            const confirmDeleteModal = document.getElementById('confirmDeleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // When the modal is shown, set the delete URL
            confirmDeleteModal.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget; // Button that triggered the modal
                const deleteUrl = button.getAttribute('data-delete-url'); // Extract info from data-* attributes

                // Update the confirm button's href with the delete URL
                confirmDeleteBtn.setAttribute('href', deleteUrl);
            });
        });
    </script>

</body>
</html>
