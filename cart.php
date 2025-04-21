<?php
session_start();
error_reporting(0);
include("connection/connect.php");

// Add to Cart
if ($_GET['action'] == "add" && !empty($_POST)) {
    $dish_id = $_GET['id'];
    $res_id = $_GET['res_id'];

    $title = $_POST['title'];
    $price = $_POST['price'];
    $slogan = $_POST['slogan'];
    $img = $_POST['img'];
    $qty = $_POST['quantity'];

    // Ensure the image path is set correctly
    $img_path = 'images/dishes/' . basename($img); // Use basename to avoid directory traversal issues

    // Add or update the cart session
    if (isset($_SESSION['cart'][$dish_id])) {
        // If the item is already in the cart, update the quantity
        $_SESSION['cart'][$dish_id]['quantity'] += $qty;
    } else {
        // Otherwise, add the item to the cart
        $_SESSION['cart'][$dish_id] = array(
            'name' => $title,
            'price' => $price,
            'slogan' => $slogan,
            'image' => $img_path,
            'quantity' => $qty,
            'res_id' => $res_id
        );
    }

    header("Location: cart.php");
    exit();
}

// Delete from Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $dishId = $_POST['dishId'];
    if (isset($_SESSION['cart'][$dishId])) {
        unset($_SESSION['cart'][$dishId]);
        echo "Deleted";
    } else {
        echo "Dish not found in cart.";
    }
    exit();
}

// Handle Purchase of Single Dish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'purchase') {
    $dishId = $_POST['dishId'];
    if (isset($_SESSION['cart'][$dishId])) {
        $_SESSION['purchase_item'] = $_SESSION['cart'][$dishId];
        $_SESSION['purchase_item']['id'] = $dishId; // optional, store dish id if needed
        header("Location: checkout.php");
        exit();
    } else {
        echo "<script>alert('Dish not found.');</script>";
    }
}

// Update Quantity in Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $dishId = $_POST['dishId'];
    $qty = (int)$_POST['quantity'];

    if (isset($_SESSION['cart'][$dishId])) {
        $_SESSION['cart'][$dishId]['quantity'] = $qty; // Update the quantity in session
        echo "Updated";
    } else {
        echo "Dish not found in cart.";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Food Ordering</title>
    <style>
        .bg_image {
            background-size: cover;
            background-position: center;
        }

        /* Navbar general styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .navbar-brand img {
            width: 100px !important;
            height: 50px !important;
        }

        .navbar-collapse {
            display: flex;
            justify-content: center;
        }

        .navbar-nav {
            display: flex;
            justify-content: right;
            flex-grow: 1;
        }

        /* Styling for nav-item and adding padding/margin */
        .nav-item {
            margin: 0 15px;
            padding: 10px;
        }

        /* Link styling */
        .nav-link {
            color: #fff;
            font-size: 16px;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }

        /* Hover effect for links */
        .nav-link:hover {
            color: #f8f9fa;
        }

        /* Adjust navbar when screen is small (mobile view) */
        @media (max-width: 767px) {
            .navbar-nav {
                flex-direction: column;
                align-items: center;
            }

            /* Adjust margin for small screens */
            .nav-item {
                margin: 10px 0;
                padding: 8px 15px;
            }
        }

        /* Cart Table Styles */
        .cart-list table {
            width: 100%;
            margin-top: 10px;
        }

        .cart-list table th, .cart-list table td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .cart-list table th {
            background-color: #f8f9fa;
        }

        .dish-row {
            border-bottom: 1px solid #ddd;
        }

        .dish-remove {
            cursor: pointer;
        }

        .dish-remove a {
            color: #f00;
        }

        .input-group-sm {
            width: 120px;
        }

        .quantity {
            text-align: center;
        }

        .cart-total {
            margin-top: 20px;
        }
        
        .cart-list table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse; /* Ensure proper table structure */
        }

        .cart-list table th, .cart-list table td {
            text-align: center;
            vertical-align: middle;
            padding: 20px;
            border: 1px solid #ddd; /* Add borders to separate table cells */
        }

        .cart-list table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .container.mt-5.pt-5 {
            padding-top: 25px;
        }
    </style>
    <script src="js/jquery.min.js"></script>
</head>
<body>

<!-- Header Section (Navbar) -->
<header id="header" class="header-scroll top-header headrom">
    <nav class="navbar navbar-dark navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
            <a class="navbar-brand" href="index.php">
                <img class="img-rounded" src="images/green1.png" alt="">
            </a>
            <div class="collapse navbar-collapse" id="mainNavbarCollapse">
                <ul class="nav navbar-nav ml-auto"> <!-- ml-auto to align items to the right -->
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="restaurants.php">Restaurants</a>
                    </li>
                    <?php
                    if(empty($_SESSION["user_id"])) { // if user is not logged in
                        echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a></li>';
                        echo '<li class="nav-item"><a href="registration.php" class="nav-link active">Sign Up</a></li>';
                    } else { // if user is logged in
                        echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">Your Orders</a></li>';
                        echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Main Content (Cart) -->
<div class="container mt-5 pt-5">
    <h2 class="text-center mb-4">Your Shopping Cart</h2>
    <div class="cart-list">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Image</th>
                    <th>Dish Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $cart = $_SESSION['cart'] ?? [];
            $total_price = 0;

            if (!empty($cart)) {
                foreach ($cart as $dishId => $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_price += $subtotal;
                    ?>
                    <tr class="dish-row" data-dish-id="<?= htmlspecialchars($dishId) ?>">
                        <td>
                            <a href="#" class="delete-dish text-danger" data-dishid="<?= htmlspecialchars($dishId) ?>"><i class="fa fa-trash"></i></a>
                        </td>
                        <td>
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 60px; height: 60px;">
                        </td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="price">$<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <div class="input-group input-group-sm mx-auto">
                                <button class="btn btn-outline-secondary quantity-minus">-</button>
                                <input type="text" class="quantity form-control text-center" value="<?= $item['quantity'] ?>" min="1" max="100">
                                <button class="btn btn-outline-secondary quantity-plus">+</button>
                            </div>
                        </td>
                        <td class="total">$<?= number_format($subtotal, 2) ?></td>
                        <td>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="dishId" value="<?= htmlspecialchars($dishId) ?>">
                                <input type="hidden" name="action" value="purchase">
                                <button type="submit" class="btn btn-success btn-sm">Purchase</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
            } else {
                echo '<tr><td colspan="7">Your cart is empty!</td></tr>';
            }
            ?>
            </tbody>
        </table>

        <?php if (!empty($cart)) { ?>
            <div class="cart-total text-center">
                <h4>Total: <span id="cart-total">$<?= number_format($total_price, 2); ?></span></h4>
            </div>
        <?php } ?>
    </div>
</div>

<script>
$(function() {
 function updateCartTotal() {
    let total = 0;
    $('.dish-row').each(function() {
        const price = parseFloat($(this).find('.price').text().replace('$',''));
        const qty = parseInt($(this).find('input.quantity').val()) || 1;
        const line = price * qty;
        $(this).find('.total').text('$' + line.toFixed(2));
        total += line;
    });
    $('#cart-total').text('$' + total.toFixed(2));
}


$('.quantity').on('change', function() {
    const dishId = $(this).closest('.dish-row').data('dish-id');
    const qty = $(this).val();

    $.post('cart.php', { action: 'update_quantity', dishId: dishId, quantity: qty }, function(response) {
        if (response.includes('Updated')) {
            location.reload();
        } else {
            alert('Error updating quantity');
        }
    });
});
    $('.quantity-minus').on('click', function() {
        const $input = $(this).siblings('input.quantity');
        const val = parseInt($input.val()) || 1;
        if (val > 1) $input.val(val - 1);
        updateCartTotal();
    });

    $('.quantity-plus').on('click', function() {
        const $input = $(this).siblings('input.quantity');
        $input.val((parseInt($input.val()) || 1) + 1);
        updateCartTotal();
    });

    $(document).on('click', '.delete-dish', function(e) {
        e.preventDefault();
        const dishId = $(this).data('dishid');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t get it back!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('cart.php', { action: 'delete', dishId: dishId }, function(res) {
                    if (res.includes('Deleted')) location.reload();
                    else Swal.fire('Error', res, 'error');
                });
            }
        });
    });

    updateCartTotal();
});
</script>
 <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>