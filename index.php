<?php
// Include database connection and common functions
include('./includes/db_connect.php');
include('./functions/common_functions.php');

// Start a session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define ('BASE_URL3', 'http://localhost/zntech/');
// Fetch all products from the database
try {
    $query = "SELECT * FROM product LIMIT 6"; // Replace 'products' with your table name
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - ZN Tech</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/?size=100&id=SP0rgjdOWCLf&format=png&color=000000">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<?php include('./includes/header.php'); // Include header with navigation ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Our Products</h1>
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <img src="./admin/product_images/<?= htmlspecialchars($product['product_image_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text">
                                <?= htmlspecialchars(substr($product['product_description'], 0, 100)); ?>...
                            </p>
                            <p class="text-success font-weight-bold">
                                Rs <?= htmlspecialchars($product['product_price']); ?>
                            </p>
                            <a href="product_details.php?id=<?= $product['product_id']; ?>" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                            <a href="<?= BASE_URL3; ?>user/add_to_cart.php?id=<?= $product['product_id']; ?>" class="btn btn-success btn-sm">
                                Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No products available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Include footer -->
<?php include('./includes/footer.php'); ?>


<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
