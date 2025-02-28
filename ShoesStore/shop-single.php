<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "site_pantofi";

// Creare conexiune
$conn = new mysqli($servername, $username, $password, $database);

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Fetch shoe details from the database
if (isset($_GET['id'])) {
    $shoe_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM papuci WHERE id = ?");
    $stmt->bind_param("i", $shoe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shoe = $result->fetch_assoc();
    $stmt->close();
} else {
    die("ID-ul papucului nu a fost specificat.");
}

$conn->close();

// Check if the shoe is sold out
$isSoldOut = ($shoe['m38'] == 0 && $shoe['m39'] == 0 && $shoe['m40'] == 0 && $shoe['m41'] == 0 && $shoe['m42'] == 0 && $shoe['m43'] == 0 && $shoe['m44'] == 0 && $shoe['m45'] == 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($shoe['nume_papuc']); ?> - epantofi.ro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/epantofi_logo.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <style>
        .zoom-container {
            position: relative;
            overflow: hidden;
        }
        .zoom-image {
            transition: transform 0.2s ease-in-out;
        }
        .zoom-container:hover .zoom-image {
            transform: scale(1.5);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Close Header -->

    <!-- Shoe Details -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-6">
                <div class="zoom-container">
                    <img class="img-fluid zoom-image" src="assets/img/<?php echo htmlspecialchars($shoe['imagine']); ?>" alt="<?php echo htmlspecialchars($shoe['nume_papuc']); ?>">
                </div>            
            </div>
            <div class="col-lg-6">
                <h1 class="h2"><?php echo htmlspecialchars($shoe['nume_papuc']); ?></h1>
                <?php if ($isSoldOut): ?>
                    <p class="h3 text-danger">SOLD OUT</p>
                <?php else: ?>
                    <p class="h3"><?php echo htmlspecialchars($shoe['pret']); ?> RON</p>
                <?php endif; ?>
                <ul class="list-unstyled d-flex justify-content-start mb-1">
                    <?php
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $shoe['rating']) {
                            echo '<i class="text-warning fa fa-star"></i>';
                        } else {
                            echo '<i class="text-muted fa fa-star"></i>';
                        }
                    }
                    ?>
                </ul>
                <p><?php echo htmlspecialchars($shoe['descriere']); ?></p>
                
                <?php if (!$isSoldOut): ?>
                    <!-- Size Selection -->
                    <div class="mb-3">
                        <label for="size" class="form-label">Select Size:</label>
                        <div id="size-options" class="d-flex flex-wrap">
                            <?php
                            $sizes = ['m38' => 38, 'm39' => 39, 'm40' => 40, 'm41' => 41, 'm42' => 42, 'm43' => 43, 'm44' => 44, 'm45' => 45];
                            foreach ($sizes as $size_key => $size_value) {
                                if ($shoe[$size_key] > 0) {
                                    echo '<div class="form-check form-check-inline">';
                                    echo '<input class="form-check-input" type="radio" name="size" id="size' . $size_value . '" value="' . $size_value . '" data-stock="' . $shoe[$size_key] . '">';
                                    echo '<label class="form-check-label btn btn-success" for="size' . $size_value . '">' . $size_value . '</label>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Quantity Selection -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="decrement">-</button>
                            <input type="text" class="form-control text-center" id="quantity" value="1" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="increment">+</button>
                        </div>
                    </div>
                    
                    <!-- Hidden input for shoe ID -->
                    <input type="hidden" id="shoe-id" value="<?php echo $shoe_id; ?>">

                    <!-- Add to Cart Button -->
                    <button class="btn btn-primary" id="add-to-cart">Add to Cart</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- End Shoe Details -->

    <!-- Back to Shop Button -->
    <div class="container mb-5">
        <div class="row">
            <div class="col text-right">
                <a href="shop.php" class="btn btn-secondary mt-3">Back to Shop</a>
            </div>
        </div>
    </div>

    <!-- Start Footer -->
    <footer class="bg-dark" id="tempaltemo_footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-success border-bottom pb-3 border-light logo">epantofi.ro</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none" href="tel:010-020-0340">+40 0732 456 890</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none" href="mailto:info@company.com">epantofi@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="w-100 bg-dark py-3">
        </div>
    </footer>
    <!-- End Footer -->

    <!-- Start Script -->
    <script src="assets/js/jquery-1.11.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quantity increment and decrement
        document.getElementById('increment').addEventListener('click', function() {
            var quantity = document.getElementById('quantity');
            quantity.value = parseInt(quantity.value) + 1;
        });

        document.getElementById('decrement').addEventListener('click', function() {
            var quantity = document.getElementById('quantity');
            if (parseInt(quantity.value) > 1) {
                quantity.value = parseInt(quantity.value) - 1;
            }
        });

        // Add to Cart button functionality (to be implemented)
        // Add to Cart button functionality
        document.getElementById('add-to-cart').addEventListener('click', function() {
            var selectedSize = document.querySelector('input[name="size"]:checked');
            var quantity = document.getElementById('quantity').value;
            var shoeId = document.getElementById('shoe-id').value; // Assuming you have a hidden input with the shoe ID
            if (selectedSize) {
                var stock = selectedSize.getAttribute('data-stock');
                if (parseInt(quantity) <= parseInt(stock)) {
                    // Send AJAX request to add to cart
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'add_to_cart.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                alert(xhr.responseText);
                            } else {
                                alert('Error: ' + xhr.status);
                            }
                        }
                    };
                    xhr.send('shoe_id=' + shoeId + '&size=' + selectedSize.value + '&quantity=' + quantity);
                } else {
                    alert('The quantity exceeds the available stock.');
                }
            } else {
                alert('Please select a size.');
            }
        });
    </script>
    <!-- End Script -->
</body>
</html>