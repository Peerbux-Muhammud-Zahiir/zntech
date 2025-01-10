<?php
// Start session
session_start();

// Include database connection and utility functions
include('../includes/db_connect.php');

// Ensure the user is an admin
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access. Only administrators can access this page.");
}

// Handle review actions (approve/reject/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = (int)$_POST['review_id'];
    $action = $_POST['action'];

    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE review SET status = 'approved' WHERE review_id = :review_id");
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE review SET status = 'rejected' WHERE review_id = :review_id");
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM review WHERE review_id = :review_id");
        } else {
            throw new Exception("Invalid action.");
        }

        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->execute();
        $message = "Action '$action' successfully performed on review ID $review_id.";
    } catch (Exception $e) {
        $error_message = "Error performing action: " . $e->getMessage();
    }
}

// Fetch all reviews
try {
    $stmt = $pdo->prepare("
        SELECT review.*, user.user_name, product.product_name 
        FROM review 
        JOIN user ON review.user_id = user.user_id 
        JOIN product ON review.product_id = product.product_id
    ");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching reviews: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" href="https://img.icons8.com/?size=100&id=SP0rgjdOWCLf&format=png&color=000000">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
 

    <div class="container mt-5">
        <h1 class="mb-4">Manage Reviews</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Review ID</th>
                    <th>Product</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= htmlspecialchars($review['review_id']); ?></td>
                        <td><?= htmlspecialchars($review['product_name']); ?></td>
                        <td><?= htmlspecialchars($review['user_name']); ?></td>
                        <td><?= htmlspecialchars($review['review_rating']); ?></td>
                        <td><?= nl2br(htmlspecialchars($review['review_text'])); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="review_id" value="<?= $review['review_id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Approve</button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="review_id" value="<?= $review['review_id']; ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-warning btn-sm"><i class="fa fa-times"></i> Reject</button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="review_id" value="<?= $review['review_id']; ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
