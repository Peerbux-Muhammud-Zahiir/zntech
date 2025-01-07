<?php
// Start a session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../includes/db_connect.php');

// Check if the product ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Fetch product details from the database
    try {
        $query = "SELECT * FROM product WHERE product_id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Prepare cart item
            $cart_item = [
                'id' => $product['product_id'],
                'name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => 1
            ];

            // Initialize the cart if it doesn't exist
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if the product is already in the cart
            $product_exists = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $cart_item['id']) {
                    $item['quantity'] += 1; // Increment quantity
                    $product_exists = true;
                    break;
                }
            }

            // Add the product to the cart if not already present
            if (!$product_exists) {
                $_SESSION['cart'][] = $cart_item;
            }

            // Redirect to the cart page with a success message
            $_SESSION['success_message'] = $product['product_name'] . " added to cart.";
            header("Location: ../cart.php");
            exit;
        } else {
            // Redirect to products page if product not found
            $_SESSION['error_message'] = "Product not found.";
            header("Location: products.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Error fetching product: " . $e->getMessage());
    }
} else {
    // Redirect to products page if no product ID is provided
    $_SESSION['error_message'] = "Invalid product.";
    header("Location: products.php");
    exit;
}
?>
