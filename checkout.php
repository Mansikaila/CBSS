<?php
include("connection/connect.php");
session_start();
error_reporting(0);

// Check login
if (empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit();
}

// Ensure item is present in the purchase session
if (!isset($_SESSION['purchase_item'])) {
    echo "<script>alert('No item selected for purchase.'); window.location.href='cart.php';</script>";
    exit();
}

$item = $_SESSION['purchase_item'];
$title = $item['name'];
$quantity = (int)$item['quantity']; // Fetch updated quantity
$price = (float)$item['price']; // Fetch updated price
$total = $quantity * $price; // Calculate the correct total price

// Handle order submission
if (isset($_POST['submit'])) {
    $uid = $_SESSION["user_id"];
    $stmt = $db->prepare("INSERT INTO users_orders (u_id, title, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isid", $uid, $title, $quantity, $total);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        unset($_SESSION['purchase_item']); // Clear purchase item from session
        echo "<script>alert('Order placed successfully!'); window.location.href='your_orders.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to place order.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Order Checkout</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Checkout Summary</h2>
    <form method="post">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Dish</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($title) ?></td>
                    <td><?= htmlspecialchars($quantity) ?></td>
                    <td>₹<?= number_format($price, 2) ?></td>
                    <td>₹<?= number_format($total, 2) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="form-group">
            <label><strong>Select Payment Method:</strong></label><br>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" value="COD" id="cod" checked>
                <label class="form-check-label" for="cod">Cash on Delivery</label>
            </div>
        </div>
        <button type="submit" name="submit" class="btn btn-success btn-block">Place Order</button>
    </form>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>