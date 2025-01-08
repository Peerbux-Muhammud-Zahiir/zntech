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

    // Fetch existing product details
    $stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<script>alert('Product not found.'); window.location.href = 'manage_products.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'manage_products.php';</script>";
    exit;
}

// Handle form submission
if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $new_image = $_FILES['product_image']['name'];
    $temp_image = $_FILES['product_image']['tmp_name'];

    // Validate fields
    if (empty($product_name) || empty($product_description) || empty($product_price) || empty($product_stock)) {
        echo "<script>alert('Fields should not be empty');</script>";
    } else {
        // Handle image replacement if a new image is uploaded
        $updated_image_url = $product['product_image_url'];
        if (!empty($new_image)) {
            $target_dir = "./product_images/";
            $target_file = $target_dir . basename($new_image);

            // Check if the new image already exists in the folder
            if (!file_exists($target_file)) {
                // Delete the old image
                $old_image_path = "./product_images/" . $product['product_image_url'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }

                // Move the new image to the folder
                if (move_uploaded_file($temp_image, $target_file)) {
                    $updated_image_url = basename($new_image);
                } else {
                    echo "<script>alert('Error uploading the new image.');</script>";
                    exit;
                }
            } else {
                // Use the existing image file if it already exists
                $updated_image_url = basename($new_image);
            }
        }

        // Update the product in the database
        $sql="
            UPDATE product
            SET 
                product_name = :product_name,
                product_description = :product_description,
                product_price = :product_price,
                product_stock = :product_stock,
                product_image_url = :product_image_url
            WHERE product_id = :product_id
        ";
        $stmt = $pdo->prepare($sql);
        // $update_params = [
        //     ':product_name' => $product_name,
        //     ':product_description' => $product_description,
        //     ':product_price' => $product_price,
        //     ':product_stock' => $product_stock,
        //     ':product_image_url' => $updated_image_url,
        //     ':product_id' => $product_id,
        // ];

        $stmt->bindParam(':product_name',$product_name);
        $stmt->bindParam(':product_description',$product_description);
        $stmt->bindParam(':product_price',$product_price);
        $stmt->bindParam(':product_stock',$product_stock);
        $stmt->bindParam(':product_image_url',$updated_image_url);
        $stmt->bindParam(':product_id',$product_id);
    

        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully.'); window.location.href = 'manage_products.php';</script>";
        } else {
            echo "<script>alert('Error updating product.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
</head>
<body>
    <div class="container py-4">
        <h2>Edit Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <!-- Product Name -->
            <div class="form-outline mb-4">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>
            <!-- Product Description -->
            <div class="form-outline mb-4">
                <label for="product_description" class="form-label">Product Description</label>
                <textarea name="product_description" id="product_description" class="form-control" required><?= htmlspecialchars($product['product_description']) ?></textarea>
            </div>
            <!-- Product Price -->
            <div class="form-outline mb-4">
                <label for="product_price" class="form-label">Price</label>
                <input type="number" step="0.01" name="product_price" id="product_price" class="form-control" value="<?= htmlspecialchars($product['product_price']) ?>" required>
            </div>
            <!-- Product Stock -->
            <div class="form-outline mb-4">
                <label for="product_stock" class="form-label">Stock Quantity</label>
                <input type="number" name="product_stock" id="product_stock" class="form-control" value="<?= htmlspecialchars($product['product_stock']) ?>" required>
            </div>
            <!-- Product Image -->
            <div class="form-outline mb-4">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" name="product_image" id="product_image" class="form-control">
                <small>Current Image: <?= htmlspecialchars($product['product_image_url']) ?></small>
            </div>
            <!-- Submit Button -->
            <input type="submit" value="Update Product" name="update_product" class="btn btn-primary">
        </form>
    </div>
</body>
</html>
