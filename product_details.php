<?php
// Start the session
session_start();
define('BASE_URL1', 'http://localhost/zntech/');

// Include database connection and utility functions
include('./includes/db_connect.php');

// Ensure the `id` parameter (product_id) is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Product ID is missing.');
}

$product_id = (int)$_GET['id'];

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





// Fetch product reviews and calculate the average rating
try {
    $sql="
    SELECT review.*, user.user_name 
    FROM review 
    JOIN user ON review.user_id = user.user_id 
    WHERE review.product_id = :product_id
";
    $review_stmt = $pdo->prepare($sql);
    $review_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $review_stmt->execute();
    $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

    $average_rating = 0;
    $total_reviews = count($reviews);
    if ($total_reviews > 0) {
        $sum_ratings = array_sum(array_column($reviews, 'rating'));
        $average_rating = round($sum_ratings / $total_reviews, 1); // Rounded to one decimal point
    }
} catch (PDOException $e) {
    die("Error fetching reviews: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link type="icon" href="./assets/images/favicon2.png">
</head>
<body>
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

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="./admin/product_images/<?= htmlspecialchars($product['product_image_url']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>" class="img-fluid">
        </div>

        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['product_name']); ?></h2>
            <p><strong>Price:</strong> Rs <?= htmlspecialchars($product['product_price']); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($product['product_description']); ?></p>

            <!-- Rating Section -->
            <div class="mb-3">
                <strong>Average Rating: </strong>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?= ($i <= $average_rating) ? '' : '-o'; ?>"></i>
                <?php endfor; ?>
                <span>(<?= $total_reviews; ?> reviews)</span>
            </div>

            <!-- Add to Cart Button -->
            <a href="cart.php?add=<?= $product['product_id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add to Cart</a>
        </div>
    </div>

    <hr>

    <!-- Reviews Section -->
    <h3>Customer Reviews</h3>
    <?php if ($total_reviews > 0): ?>
        <div class="list-group">
            <?php foreach ($reviews as $review): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <span><strong>Rating: </strong>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= ($i <= $review['review_rating']) ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span><strong>Reviewed by:</strong> <?= htmlspecialchars($review['user_name']); ?></span>
                    </div>
                    <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($review['review_text'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No reviews yet for this product. Be the first to review!</p>
    <?php endif; ?>
<?php include('./includes/footer.php'); ?>

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
