<?php
include('../includes/db_connect.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product's image name from the database
    $stmt = $pdo->prepare("SELECT product_image_url FROM product WHERE product_id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Construct the image file path
        $image_path = "./product_images/" . $product['product_image_url'];

        // Delete the image file if it exists
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Delete the product record from the database
        $delete_stmt = $pdo->prepare("DELETE FROM product WHERE product_id = :id");
        if ($delete_stmt->execute([':id' => $product_id])) {
            echo "<script>alert('Product deleted successfully.'); window.location.href = 'manage_products.php';</script>";
        } else {
            echo "<script>alert('Error deleting product.'); window.location.href = 'manage_products.php';</script>";
        }
    } else {
        echo "<script>alert('Product not found.'); window.location.href = 'manage_products.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'manage_products.php';</script>";
}
?>
