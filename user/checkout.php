<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('../includes/db_connect.php');

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    $_SESSION['error_message'] = "Your cart is empty. Add items before proceeding to checkout.";
    header("Location: cart.php");
    exit;
}

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM user WHERE user_id = :user_id";
$user_stmt = $pdo->prepare($user_query);
$user_stmt->bindParam(':user_id', $user_id);
$user_stmt->execute();
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user exists
if (!$user) {
    $_SESSION['error_message'] = "User not found. Please log in first.";
    header("Location: login.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $delivery_address = $_POST['delivery_address'];
    $payment_method = $_POST['payment_method'];  // Added payment method

    // Validate input
    if (empty($delivery_address)) {
        $error_message = "Delivery address is required.";
    } elseif (empty($payment_method)) {
        $error_message = "Payment method is required.";
    } else {
        try {
            // Insert order into the `order` table
            $pdo->beginTransaction();

            $order_query = "INSERT INTO `order` (order_date, order_status, user_id, delivery_address) 
                            VALUES (NOW(), 'pending', :user_id, :delivery_address)";
            $order_stmt = $pdo->prepare($order_query);
            $order_stmt->execute([
                ':user_id' => $user_id,
                ':delivery_address' => $delivery_address,
            ]);

            // Get the last inserted order ID
            $order_id = $pdo->lastInsertId();

            // Insert payment into the `payment` table
            $payment_query = "INSERT INTO `payment` (payment_method, payment_status, order_id, payment_date) 
                              VALUES (:payment_method, 'pending', :order_id, NOW())";
            $payment_stmt = $pdo->prepare($payment_query);
            $payment_stmt->execute([
                ':payment_method' => $payment_method,
                ':order_id' => $order_id
            ]);

            // Insert items into `order_product` table
            $product_query = "INSERT INTO order_product (order_id, product_id, quantity) 
                              VALUES (:order_id, :product_id, :quantity)";
            $product_stmt = $pdo->prepare($product_query);

            foreach ($_SESSION['cart'] as $item) {
                $product_stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity']
                ]);
            }

            // Commit transaction
            $pdo->commit();

            // Clear cart and redirect
            unset($_SESSION['cart']);
            $_SESSION['success_message'] = "Order placed successfully!";
            header("Location: ../cart.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Failed to place the order. Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Checkout</h1>


    <h3 class="mt-4">Order Summary</h3>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $total = $item['price'] * $item['quantity'];
                    $grand_total += $total;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td>Rs <?= htmlspecialchars($item['price']); ?></td>
                        <td><?= htmlspecialchars($item['quantity']); ?></td>
                        <td>Rs <?= htmlspecialchars($total); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <!-- Error or success messages -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Checkout Form -->
    <form action="checkout.php" method="POST">
        <div class="card p-4">
                <div class="form-group">
                    <label for="delivery_address" class="form-label">Delivery Address</label>
                    <input id="delivery_address" name="delivery_address" class="form-control" rows="3" required></textarea>
                </div>

                <!-- Payment Method Selection -->
                <div class="form-group">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control w-25" required>
                        <option value="" disabled selected>Select a Payment Method</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                        <option value="pay_on_delivery">Pay on Delivery</option>
                    </select>
                </div>
             
       

        <h4 class="text-right">Grand Total: Rs <?= $grand_total; ?></h4>

        <div class="text-right">
            <button type="submit" class="btn btn-success btn-md"><i class="fa fa-dollar"></i> Checkout</button>
        </div>
       </div> 
    </form>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
