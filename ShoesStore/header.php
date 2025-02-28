<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>epantofi.ro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/epantofi_logo.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a class="navbar-brand text-success logo h1 align-self-center" href="index.php">
                epantofi.ro
            </a>
            <!--RIGHT FROM THE ICON-->
            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
                <!-- NAVBAR HOME + ABOUT + SHOP + CONTACT-->
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shop.php">Shop</a>
                        </li>
                    </ul>
                </div>
                <!-- RIGHT ICONS FOR SEARCH, CART, ACCOUNT-->
                <div class="navbar align-self-center d-flex">
                    <a class="nav-icon d-none d-lg-inline" href="#">
                        <i class="fa fa-fw fa-search text-dark mr-2"></i>
                    </a>
                    <a class="nav-icon position-relative text-decoration-none" href="cart.php">
                        <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (basename($_SERVER['PHP_SELF']) == 'profile.php'): ?>
                            <a class="nav-icon position-relative text-decoration-none btn btn-danger d-flex align-items-center justify-content-center" href="logout.php" style="padding: 5px 10px;">
                                <!-- <i class="fa fa-fw fa-sign-out text-white mr-1"></i> -->
                                <span class="text-black">Logout</span>
                            </a>
                        <?php else: ?>
                            <a class="nav-icon position-relative text-decoration-none" href="profile.php">
                                <i class="fa fa-fw fa-user text-dark mr-3"></i>
                                <span>Bun venit, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="nav-icon position-relative text-decoration-none" href="login.html">
                            <i class="fa fa-fw fa-user text-dark mr-3"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- Close Header -->