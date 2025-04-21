<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php"); // connection to db
error_reporting(0);
session_start();
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Food Ordering System</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
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
    <style>
    /* Navbar general styles */
/*
            .res{
               padding-left: 150px;
            }
*/
    .navbar {
        display: flex;
        justify-content: space-between; /* Ensure space between items */
        align-items: center;
        
        padding: 40;
    }

    .navbar-brand img {
        width: 100px !important; /* Adjusted size */
        height: 50px !important;
    }

    .navbar-collapse {
        display: flex;
        justify-content: center; /* Center the nav items */
  
    .navbar-nav {
        display: flex;
        justify-content: right;
        flex-grow: 1; /* Ensure the nav items are centered */
    }

    /* Styling for nav-item and adding padding/margin */
    .nav-item {
        margin: 10 20px; /* Space between nav items */
        padding: 10px 20px; /* Space around text */
        
    }

    /* Link styling */
    .nav-link {
        color: #fff; /* Ensure text color is white */
        font-size: 16px;
        text-transform: uppercase; /* Optional: makes text uppercase */
        transition: color 0.3s ease;
    }

    /* Hover effect for links */
    .nav-link:hover {
        color: #f8f9fa; /* Slightly lighter color on hover */
    }

    /* Adjust navbar when screen is small (mobile view) */
    @media (max-width: 767px) {
        .navbar-nav {
            flex-direction: column; /* Stack items vertically on small screens */
            align-items: center;
        }

        /* Adjust margin for small screens */
        .nav-item {
            margin: 10px 0; /* Vertical margin for small screens */
            padding: 8px 15px; /* Padding adjustment */
        }
    }
</style>
    <style>
/* Adjusting the price style */
.price {
    font-size: 18px; /* Adjust font size as needed */
    color: #000; /* Adjust color if needed */
    margin-right: 15px; /* Add spacing between price and the button */
    padding: 5px 10px; /* Add padding around the price */
    display: inline-block; /* Ensure proper spacing */
}
/* Styling the Add to Cart button */
.btn.theme-btn {
    background-color: #f8b400; /* Add a background color */
    color: #fff; /* Button text color */
    border-radius: 5px; /* Rounded corners */
    margin-left: 10px; /* Margin to separate from the price */
    padding: 8px 20px; /* Adjust padding for better size */
    font-size: 16px; /* Adjust font size */
    transition: background-color 0.3s ease; /* Smooth hover effect */
}
/* Hover effect for Add to Cart button */
.btn.theme-btn:hover {
    background-color: #d69300; /* Slightly darker hover color */
    color: #fff; /* Ensure text is still visible */
}
/* Adjusting layout in small screens */
@media (max-width: 767px) {
    .price, .btn.theme-btn {
        display: block;
        margin: 10px 0; /* Stack elements vertically */
        text-align: center; /* Center align for smaller screens */
    }
}
</style>
    <!-- Restaurant Details -->
    <?php
    $ress = mysqli_query($db, "select * from restaurant where rs_id='$_GET[res_id]'");
    $rows = mysqli_fetch_array($ress);
    ?>
    <section class="inner-page-hero bg-image" data-image-src="images/img/dish.jpeg">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 profile-img">
                        <div class="image-wrap">
                            <figure><?php echo '<img src="admin/Res_img/' . $rows['image'] . '" alt="Restaurant logo">'; ?></figure>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 profile-desc">
                        <div class="pull-left right-text white-txt">
                            <h6><a href="#"><?php echo $rows['title']; ?></a></h6>
                            <p><?php echo $rows['address']; ?></p>
                            <ul class="nav nav-inline">
                                <li class="nav-item"> <a class="nav-link active" href="#"><i class="fa fa-check"></i> Min $10.00</a></li>
                                <li class="nav-item"> <a class="nav-link" href="#"><i class="fa fa-motorcycle"></i> 30 min</a></li>
                                <li class="nav-item ratings">
                                    <a class="nav-link" href="#"> 
                                        <span>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>		
                </div>
            </div>
        </div>
    </section>
    <!-- Menu -->
    <div class="container m-t-30 res">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-12">
                <div class="menu-widget" id="2">
                    <div class="widget-heading">
                        <h3 class="widget-title text-dark">
                            POPULAR ORDERS Delicious hot food!
                        </h3>
                    </div>
                    <div>
                        <?php
                        $stmt = $db->prepare("select * from dishes where rs_id='$_GET[res_id]'");
                        $stmt->execute();
                        $products = $stmt->get_result();
                        if (!empty($products)) {
                            foreach ($products as $product) {
                        ?>
<div class="center-container">
    <div class="food-item">
        <form method="post" action="cart.php?res_id=<?php echo htmlspecialchars($_GET['res_id']); ?>&action=add&id=<?php echo htmlspecialchars($product['d_id']); ?>">
            <div class="row">
                <!-- Image Section -->
                <div class="col-xs-10 col-sm-10">
                    <div class="media">
                        <div class="media-left" style="width: 120px;">
                            <img src="admin/Res_img/dishes/<?php echo htmlspecialchars($product['img']); ?>" 
                                 alt="Food logo" class="media-object" 
                                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                        </div>
                        <div class="media-body" style="padding-left: 20px;">
                            <h4 class="media-heading"><?php echo htmlspecialchars($product['title']); ?></h4>
                            <p><?php echo htmlspecialchars($product['slogan']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Price & Add to Cart Section -->
                <div class="col-xs-12 col-sm-6 text-right" style="margin-top: 15px;">
                    <span class="price" style="font-weight: bold; font-size: 1.1rem; color: #28a745;">
                        $<?php echo htmlspecialchars($product['price']); ?>
                    </span>

                    <!-- Hidden Inputs for Passing Full Dish Info -->
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($product['title']); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                    <input type="hidden" name="slogan" value="<?php echo htmlspecialchars($product['slogan']); ?>">
                    <input type="hidden" name="img" value="<?php echo htmlspecialchars($product['img']); ?>">
                    <input type="hidden" name="quantity" value="1" />

                    <input type="submit" class="btn theme-btn" value="Add to cart" />
                </div>
            </div>
        </form>
    </div>
</div>

                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row top-footer">
                <div class="col-xs-12 col-sm-3 footer-logo-block color-gray">
                    <a href="#">
                        <img src="images/green1.png" alt="Footer logo" class="footer-logo">
                    </a>
                    <span>Order Delivery &amp; Take-Out</span>
                </div>
                <div class="col-xs-12 col-sm-2 pages color-gray">
                    <h5>Pages</h5>
                    <ul>
                        <li><a href="#">Search results page</a></li>
                        <li><a href="#">User Sign Up Page</a></li>
                        <li><a href="#">Pricing page</a></li>
                        <li><a href="#">Make order</a></li>
                        <li><a href="#">Add to cart</a></li>
                    </ul>
                </div>
            </div>
            <div class="bottom-footer">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 address color-gray">
                        <h5>Address</h5>
                        <p>Nkshtra-5,Shadhu Wasvani Road,Rajkot-360005</p>
                        <h5>Phone: <a href="tel:+080000012222">9173633311</a></h5>
                    </div>
                    <div class="col-xs-12 col-sm-5 additional-info color-gray">
                        <h5>Additional Information</h5>
                        <p>Join the thousands of other restaurants who benefit from having their menus on TakeOff</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- JavaScript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>
</html>