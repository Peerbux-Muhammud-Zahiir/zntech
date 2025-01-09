<?php
include('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if (isset($_POST['insert_product'])) {
    // Retrieve form data
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    // Retrieve image data
    $product_image = $_FILES['product_image']['name'];
    $temp_image = $_FILES['product_image']['tmp_name'];

    // Check if fields are empty
    if ($product_name == '' || $product_description == '' || empty($product_price) || empty($product_quantity) || empty($product_image)) {
        echo "<script>alert('Fields should not be empty');</script>";
        exit();
    } else {
        // Set target directory for the uploaded image
        $target_dir = "./product_images/";
        $target_file = $target_dir . basename($product_image);

        // Check if the image already exists
        if (file_exists($target_file)) {
            // Store only the image name if it already exists
            $image_url = basename($product_image);
        } else {
            // Move the uploaded file
            if (move_uploaded_file($temp_image, $target_file)) {
                $image_url = basename($product_image);
            } else {
                echo "<script>alert('Error uploading the file.');</script>";
                exit();
            }
        }

        // Insert the product into the database
        $sql = "INSERT INTO `product` (product_name, product_description, product_price, product_stock, product_image_url) 
                VALUES (:product_name, :product_description, :product_price, :product_quantity, :product_image_url)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':product_description', $product_description);
        $stmt->bindParam(':product_price', $product_price);
        $stmt->bindParam(':product_quantity', $product_quantity);
        $stmt->bindParam(':product_image_url', $image_url);

        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully.'); window.location.href = 'manage_products.php';</script>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<script>alert('Error inserting product: " . $errorInfo[2] . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
</head>
<body class="bg-light">
<div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Add Product</h3>
</div>
        <form action="" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="product_description" class="form-label">Product Description</label>
                <input type="text" name="product_description" id="product_description" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="product_price" class="form-label">Price</label>
                <input type="number" name="product_price" id="product_price" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="product_quantity" class="form-label">Quantity</label>
                <input type="number" name="product_quantity" id="product_quantity" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" name="product_image" id="product_image" class="form-control" required>
            </div>
            <div class="text-center">
            <button type="submit" name="insert_product" class="btn btn-success"><i class="fa fa-plus"></i> Add</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href = 'manage_products.php';"><i class="fa fa-xmark"></i> Cancel</button>
</div>
        </div>
        </form>
    </div>
</body>
</html>
