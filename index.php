<?php
// Include database connection and common functions
include('./includes/db_connect.php');
include('./functions/common_functions.php');

// Start a session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('BASE_URL3', 'http://localhost/zntech/');

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
    <title>ZN Tech</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="./assets/css/styles.css">
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

<?php include('./includes/header.php'); // Include header with navigation ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-left" style="color: #007BFF;">Our Products</h1>
        <form action="search.php" method="get">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Search..." name="search" required>
                <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
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
                            <a href="<?= BASE_URL3; ?>user/add_to_cart.php?id=<?= $product['product_id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add to Cart
                            </a>
                            <a href="product_details.php?id=<?= $product['product_id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-info-circle"></i> View Details
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
<!-- Preloader Script -->
<script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('preloader').style.display = 'none';
        }, 500); // Wait for 1 second before hiding the preloader
    });
</script>
</body>
</html>
