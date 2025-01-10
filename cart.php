<?php
// Start session
session_start();
define('BASE_URL2', 'http://localhost/zntech/');

// Include database connection and common functions
include('./includes/db_connect.php');

// Handle removing items from the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['success_message'] = "Item removed from cart.";
            header("Location: cart.php");
            exit;
        }
    }
}

// Update cart quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'], $_POST['quantities'])) {
    foreach ($_POST['quantities'] as $key => $quantity) {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = max(1, intval($quantity)); // Ensure quantity is at least 1
        }
    }
    $_SESSION['success_message'] = "Cart updated successfully.";
    header("Location: cart.php");
    exit;
}

// Fetch user's purchase history if logged in
$user_id = $_SESSION['user_id'] ?? null;
$purchase_history = [];
$reviews_allowed = [];

if ($user_id) {
    $history_query = "
        SELECT o.order_id, o.order_date, o.order_status, p.product_name, p.product_price, op.quantity, 
               p.product_image_url, p.product_id, pay.payment_status
        FROM `order` o
        JOIN order_product op ON o.order_id = op.order_id
        JOIN product p ON op.product_id = p.product_id
        JOIN payment pay ON o.order_id = pay.order_id
        WHERE o.user_id = :user_id
        ORDER BY o.order_date DESC";
    $history_stmt = $pdo->prepare($history_query);
    $history_stmt->execute([':user_id' => $user_id]);
    $purchase_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Determine if review is allowed based on order_status and payment_status
    foreach ($purchase_history as $history) {
        if ($history['order_status'] == 'delivered' && $history['payment_status'] == 'completed') {
            $reviews_allowed[] = [
                'order_id' => $history['order_id'],
                'product_id' => $history['product_id'],
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=F6ULPz8GgDMP&format=png&color=bf40bf">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
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
    <h1 class="text-center mb-4">Shopping Cart</h1>

    <!-- Success or error messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Cart Table -->
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <h3>Your Cart</h3>
        <form action="cart.php" method="POST">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $key => $item):
                        $total = $item['price'] * $item['quantity'];
                        $grand_total += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td>Rs <?= htmlspecialchars($item['price']); ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $key; ?>]" value="<?= htmlspecialchars($item['quantity']); ?>" min="1" class="form-control" style="width: 80px;">
                            </td>
                            <td>Rs <?= htmlspecialchars($total); ?></td>
                            <td>
                            <button type="submit" name="update_cart" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Update Cart</button>
                                <a href="cart.php?remove=<?= $item['id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h4 class="text-right">Grand Total: Rs <?= $grand_total; ?></h4>
            <div class="text-right">

                <a href="<?= BASE_URL2; ?>user/checkout.php" class="btn btn-success">Place Order</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="products.php">Start shopping</a>.
        </div>
    <?php endif; ?>

    <!-- Purchase History -->
    <h3 class="mt-5">Purchase History</h3>
    <?php if (!empty($purchase_history)): ?>
        <table class="table table-bordered">
            <thead class="thead-light"> 
                <tr>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchase_history as $history): ?>
                    <tr>
                        <td><?= htmlspecialchars($history['order_date']); ?></td>
                        <td><?= htmlspecialchars($history['order_status']); ?></td>
                        <td><?= htmlspecialchars($history['product_name']); ?></td>
                        <td><img src="./admin/product_images/<?= htmlspecialchars($history['product_image_url']); ?>" alt="<?= htmlspecialchars($history['product_name']); ?>" style="height: 50px;"></td>
                        <td>Rs <?= htmlspecialchars($history['product_price']); ?></td>
                        <td><?= htmlspecialchars($history['quantity']); ?></td>
                        <td>
                            <a href="review.php?order_id=<?= htmlspecialchars($history['order_id']); ?>&product_id=<?= htmlspecialchars($history['product_id']); ?>" 
                               class="btn btn-success btn-sm"
                               <?php
                                   $is_allowed = false;
                                   foreach ($reviews_allowed as $allowed) {
                                       if ($allowed['order_id'] == $history['order_id'] && $allowed['product_id'] == $history['product_id']) {
                                           $is_allowed = true;
                                           break;
                                       }
                                   }
                                   echo $is_allowed ? '' : 'disabled';
                               ?>>
                                <i class="fa fa-pencil"></i> Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">
            You have not made any purchases yet.
        </div>
    <?php endif; ?>
</div>

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
