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
        $uploadOk = 1;

        // Check if the file is an image
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (getimagesize($temp_image) === false) {
            $uploadOk = 0;
            echo "<script>alert('File is not an image.');</script>";
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            echo "<script>alert('File already exists.');</script>";
        }

        // Check file size (5MB limit)
        if ($_FILES["product_image"]["size"] > 5000000) {
            $uploadOk = 0;
            echo "<script>alert('File is too large.');</script>";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
            $uploadOk = 0;
            echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
        }

        // If everything is okay, move the file
        if ($uploadOk == 1) {
            if (move_uploaded_file($temp_image, $target_file)) {
                // Prepare SQL query
                $sql = "INSERT INTO `product` (product_name, product_description, product_price, product_stock, product_image_url) 
                        VALUES (:product_name, :product_description, :product_price, :product_quantity, :product_image_url)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':product_description', $product_description);
                $stmt->bindParam(':product_price', $product_price);
                $stmt->bindParam(':product_quantity', $product_quantity);
                $stmt->bindParam(':product_image_url', $target_file);

                if ($stmt->execute()) {
                    echo "<script>alert('Product added successfully.'); window.location.href = 'manage_products.php';</script>";
                } else {
                    $errorInfo = $stmt->errorInfo();
                    echo "<script>alert('Error inserting product: " . $errorInfo[2] . "');</script>";
                }
            } else {
                echo "<script>alert('Error uploading the file.');</script>";
            }
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
</head>
<body>
    <div class="container py-4">
        <h2 class="text-center mb-4">Add New Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <!-- Product Name -->
                    <div class="form-group mb-4">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required>
                    </div>

                    <!-- Product Description -->
                    <div class="form-group mb-4">
                        <label for="product_description" class="form-label">Product Description</label>
                        <textarea name="product_description" id="product_description" class="form-control" rows="4" required></textarea>
                    </div>

                    <!-- Product Price -->
                    <div class="form-group mb-4">
                        <label for="product_price" class="form-label">Price</label>
                        <input type="number" name="product_price" id="product_price" class="form-control" required>
                    </div>

                    <!-- Product Quantity -->
                    <div class="form-group mb-4">
                        <label for="product_quantity" class="form-label">Quantity</label>
                        <input type="number" name="product_quantity" id="product_quantity" class="form-control" required>
                    </div>

                    <!-- Product Image -->
                    <div class="form-group mb-4">
                        <label for="product_image" class="form-label">Product Image</label>
                        <input type="file" name="product_image" id="product_image" class="form-control" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <input type="submit" value="Add Product" name="insert_product" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
