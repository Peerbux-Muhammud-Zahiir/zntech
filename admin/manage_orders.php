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

// Fetch all orders from the database
$sql_orders = "SELECT * FROM `order` JOIN `user` ON `order`.`user_id` = `user`.`user_id`";
$stmt_orders = $pdo->query($sql_orders);
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Handle delete order request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $order_id = intval($_GET['delete']);

    // Delete the order from the database
    $sql_delete = "DELETE FROM `order` WHERE `order_id` = :order_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':order_id', $order_id);
    $stmt_delete->execute();

    $_SESSION['success_message'] = "Order has been deleted successfully.";
    header("Location: manage_orders.php");
    exit;
}

// Handle update order status request
if (isset($_GET['update']) && is_numeric($_GET['update'])) {
    $order_id = intval($_GET['update']);
    $new_status = $_GET['status']; // 'shipped', 'pending', etc.

    // Update the order status in the database
    $sql_update = "UPDATE `order` SET `order_status` = :order_status WHERE `order_id` = :order_id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':order_status', $new_status);
    $stmt_update->bindParam(':order_id', $order_id);
    $stmt_update->execute();

    $_SESSION['success_message'] = "Order status has been updated successfully.";
    header("Location: manage_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Your custom styles -->
</head>
<body>


    <div class="container mt-5">
        <h1 class="text-center">Manage Orders</h1>

        <!-- Show success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Orders Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User name</th>
                    <th>Order Date</th>
                    <th>Delivery Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']); ?></td>
                            <td><?= htmlspecialchars($order['user_name']); ?></td>
                            <td><?= htmlspecialchars($order['order_date']); ?></td>
                            <td><?= htmlspecialchars($order['delivery_address']); ?></td>
                            <td>
                                <!-- <?= htmlspecialchars($order['order_status']); ?> -->
                                <form action="manage_orders.php" method="GET" style="display:inline;">
                                    <select name="status" class="dropdown" required>
                                        <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : ''; ?>>pending</option>
                                        <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>shipped</option>
                                        <option value="delivered" <?= $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>delivered</option>
                                    </select>
                                    <input type="hidden" name="update" value="<?= $order['order_id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm mt-2">Update Status</button>
                                </form>
                            </td>
                            <td>
                                <a href="view_order.php?id=<?= $order['order_id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="manage_orders.php?delete=<?= $order['order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No orders found.</td>
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
