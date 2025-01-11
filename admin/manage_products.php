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

// Fetch all products from the database
$sql_products = "SELECT * FROM `product`";
$stmt_products = $pdo->query($sql_products);
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

// Handle delete product request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = intval($_GET['delete']);

    // Delete the product from the database
    $sql_delete = "DELETE FROM `product` WHERE `product_id` = :product_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':product_id', $product_id);
    $stmt_delete->execute();

    $_SESSION['success_message'] = "Product has been deleted successfully.";
    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css"> 
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
</head>
<body>


    <div class="container mt-5">
        <h1 class="text-center">Manage Products</h1>

        <!-- Show success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Add Product Button -->
        <span class="float-right mb-2">
            <a href="add_product.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Product</a>
        </span>

           <!-- Back to Dashboard Button -->
           <span class="float-left mb-2">
            <a href="admin_dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-home"></i> Dashboard</a>
        </span>
        <!-- Products Table -->
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_id']); ?></td>
                            <td><?= htmlspecialchars($product['product_name']); ?></td>
                            <td><img src="./product_images/<?= htmlspecialchars($product['product_image_url']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>" class="img-thumbnail" style="width: 100px;"></td>
                            <td>Rs <?= htmlspecialchars($product['product_price']); ?></td>
                            <td><?= htmlspecialchars($product['product_description']); ?></td>
                            <td><?= htmlspecialchars($product['product_stock']); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?= $product['product_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="manage_products.php?delete=<?= $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

     
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script> <!-- Bootstrap JS -->
</body>
</html>
