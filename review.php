<?php
// Start the session
session_start();
define('BASE_URL1', 'http://localhost/zntech/');

// Include database connection and utility functions
include('./includes/db_connect.php');

// Ensure the `product_id` is provided in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    die('Product ID is missing.');
}

$product_id = (int)$_GET['product_id'];
$user_id = $_SESSION['user_id'] ?? null; // Ensure user is logged in
if (!$user_id) {
    die('You must be logged in to submit a review.');
}

// Fetch product details
try {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }
} catch (PDOException $e) {
    die("Error fetching product: " . $e->getMessage());
}

// Check if the user has purchased the product
$check_query = "
    SELECT o.order_id, o.order_status, pay.payment_status
    FROM `order` o
    JOIN payment pay ON o.order_id = pay.order_id
    JOIN order_product op ON o.order_id = op.order_id
    WHERE op.product_id = :product_id 
      AND o.user_id = :user_id 
      AND o.order_status = 'delivered' 
      AND pay.payment_status = 'completed'
";
$check_stmt = $pdo->prepare($check_query);
$check_stmt->execute([
    ':product_id' => $product_id,
    ':user_id' => $user_id,
]);
$order = $check_stmt->fetch(PDO::FETCH_ASSOC);

// if (!$order) {
//     // User has not purchased or received the product
//     $_SESSION['error_message'] = 'You can only review products you have purchased and received.';
//     header("Location: cart.php");
//     exit;
// }

// Handle form submission for review
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $comment = htmlspecialchars($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $_SESSION['error_message'] = "Please provide a rating between 1 and 5.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO review (user_id, product_id, review_rating, review_text) VALUES (:user_id, :product_id, :rating, :comment)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':product_id' => $product_id,
                ':rating' => $rating,
                ':comment' => $comment
            ]);
            $_SESSION['success_message'] = "Thank you for your review!";
            header("Location: cart.php");
            exit;
        } catch (PDOException $e) {
            // $_SESSION['error_message'] = "Error submitting review: " . $e->getMessage();
            $_SESSION['error_message'] = "You have already reviewed this product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- Preloader -->
<div id="preloader">
    <div>
        <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-secondary" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-success" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-danger" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-warning" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-info" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-light" role="status"><span class="sr-only">Loading...</span></div>
        <div class="spinner-grow text-dark" role="status"><span class="sr-only">Loading...</span></div>
    </div>
</div>
<?php include('./includes/header.php'); ?>


    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
    <h3>Review Product: <?= htmlspecialchars($product['product_name']); ?></h3>
</div>
    <!-- Display messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
<div class="card-body">
    <form method="POST" action="review.php?product_id=<?= $product_id; ?>">
        
        <div class="form-group">
            <label for="rating" class="form-label">Rating (1-5)</label>
            <select id="rating" name="rating" class="form-control" required>
                <option value="">Select Rating</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>

        <div class="form-group">
            <label for="comment" class="form-label">Your Comment</label>
            <textarea id="comment" name="comment" class="form-control" " required></textarea>
        </div>
        <div class="text-center">
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Submit Review</button>
        <button class="btn btn-secondary btn-sm" onclick="window.location.href = 'cart.php';"><i class="fa fa-times"></i> Cancel</button>
        </div>

    </form>

    
</div>

</div>
</div>
</div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('preloader').style.display = 'none';
        }, 500); // Wait for 1 second before hiding the preloader
    });
</script>
</body>
</html>
