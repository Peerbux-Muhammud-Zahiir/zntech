<?php
include('../includes/db_connect.php'); // Include database connection
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch all payment records
$sql = "SELECT p.payment_id, p.payment_method, p.payment_status, p.payment_date, 
               o.order_id, o.delivery_address, o.order_date, u.user_name 
        FROM payment p
        JOIN `order` o ON p.order_id = o.order_id
        JOIN user u ON o.user_id = u.user_id
        ORDER BY p.payment_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if (isset($_POST['update_status'])) {
    $payment_id = $_POST['payment_id'];
    $new_status = $_POST['payment_status'];

    $update_sql = "UPDATE payment SET payment_status = :payment_status WHERE payment_id = :payment_id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':payment_status', $new_status);
    $update_stmt->bindParam(':payment_id', $payment_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Payment status updated successfully.'); window.location.href = 'manage_payments.php';</script>";
    } else {
        echo "<script>alert('Error updating payment status.');</script>";
    }
}

// Handle payment deletion (optional)
if (isset($_POST['delete_payment'])) {
    $payment_id = $_POST['payment_id'];

    $delete_sql = "DELETE FROM payment WHERE payment_id = :payment_id";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->bindParam(':payment_id', $payment_id);

    if ($delete_stmt->execute()) {
        echo "<script>alert('Payment deleted successfully.'); window.location.href = 'manage_payments.php';</script>";
    } else {
        echo "<script>alert('Error deleting payment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2 class="text-center mb-4">Manage Payments</h2>
    <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th>Payment ID</th>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Order Date</th>
            <th>Delivery Address</th>
            <th>Payment Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= htmlspecialchars($payment['payment_id']); ?></td>
                <td><?= htmlspecialchars($payment['order_id']); ?></td>
                <td><?= htmlspecialchars($payment['user_name']); ?></td>
                <td><?= htmlspecialchars($payment['payment_method']); ?></td>
                <td>
                    <form action="" method="post" class="d-inline">
                        <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                        <select name="payment_status" class="form-control form-control-sm">
                            <option value="pending" <?php echo ($payment['payment_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo ($payment['payment_status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="refunded" <?php echo ($payment['payment_status'] === 'refunded') ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-success"><i class="fa fa-edit"></i> Update</button>
                    </form>
                <td><?= htmlspecialchars($payment['order_date']); ?></td> 
                <td><?= htmlspecialchars($payment['delivery_address']); ?></td>
                <td><?= htmlspecialchars($payment['payment_date']); ?></td>   
                <td> 
                    <form action="" method="post" class="d-inline">
                        <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                        <button type="submit" name="delete_payment" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this payment?');"><i class="fa fa-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="text-center">
<button class="btn btn-secondary" onclick="window.location.href = 'admin_dashboard.php';"><i class="fa fa-home"></i> Dashboard</button>
</div>
</body>
</html>
