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

// Fetch shoes from the database
$sql = "SELECT * FROM papuci";
$result = $conn->query($sql);

// Fetch distinct sizes and brands
$sizes = ['38', '39', '40', '41', '42', '43', '44', '45'];
$brands = [];
$brand_sql = "SELECT DISTINCT brand FROM papuci";
$brand_result = $conn->query($brand_sql);
if ($brand_result->num_rows > 0) {
    while($row = $brand_result->fetch_assoc()) {
        $brands[] = $row['brand'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shop - epantofi.ro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/epantofi_logo.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <style>
        #sort-az {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        #slider-range {
            margin-top: 50px; /* Increased margin to ensure no overlap */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Close Header -->

    <!-- Shop Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3">
                <h1 class="h2 pb-4">Categorii</h1>
                <ul class="list-unstyled templatemo-accordion">
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Price
                            <i class="fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <div id="price-range" class="collapse show">
                            <div id="slider-range"></div>
                            <p class="mt-3">Price: <span id="price-range-value1"></span> - <span id="price-range-value2"></span> RON</p>
                        </div>
                    </li>
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Size
                            <i class="fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <div id="size-filter" class="collapse show">
                            <div class="d-flex flex-wrap">
                                <?php foreach ($sizes as $size): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input size-filter" type="checkbox" value="<?php echo $size; ?>" id="size<?php echo $size; ?>">
                                        <label class="form-check-label" for="size<?php echo $size; ?>"><?php echo $size; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Brand
                            <i class="fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <div id="brand-filter" class="collapse show">
                            <ul class="list-unstyled">
                                <?php foreach ($brands as $brand): ?>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input brand-filter" type="checkbox" value="<?php echo $brand; ?>" id="brand<?php echo $brand; ?>">
                                            <label class="form-check-label" for="brand<?php echo $brand; ?>"><?php echo $brand; ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                </ul>
                <button id="sort-az" class="btn btn-outline-dark rounded-pill">A-Z</button>
            </div>
            <div class="col-lg-9">
                <div class="row" id="products">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $isSoldOut = ($row['m38'] == 0 && $row['m39'] == 0 && $row['m40'] == 0 && $row['m41'] == 0 && $row['m42'] == 0 && $row['m43'] == 0 && $row['m44'] == 0 && $row['m45'] == 0);
                            echo '<div class="col-md-4 product" data-price="' . $row["pret"] . '" data-name="' . $row["nume_papuc"] . '" data-brand="' . $row["brand"] . '" data-sizes="' . implode(',', array_filter([$row['m38'] > 0 ? '38' : '', $row['m39'] > 0 ? '39' : '', $row['m40'] > 0 ? '40' : '', $row['m41'] > 0 ? '41' : '', $row['m42'] > 0 ? '42' : '', $row['m43'] > 0 ? '43' : '', $row['m44'] > 0 ? '44' : '', $row['m45'] > 0 ? '45' : ''])) . '">';
                            echo '<div class="card mb-4 product-wap rounded-0">';
                            echo '<div class="card rounded-0">';
                            echo '<img class="card-img rounded-0 img-fluid" src="assets/img/' . $row["imagine"] . '" alt="' . $row["nume_papuc"] . '">';
                            echo '<div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">';
                            echo '<ul class="list-unstyled">';
                            echo '<li><a class="btn btn-success text-white mt-2" href="shop-single.php?id=' . $row["id"] . '"><i class="far fa-eye"></i></a></li>';
                            echo '</ul>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-body text-left">';
                            echo '<a href="shop-single.php?id=' . $row["id"] . '" class="h3 text-decoration-none">' . $row["nume_papuc"] . '</a>';
                            echo '<ul class="list-unstyled d-flex justify-content-center mb-1">';
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $row["rating"]) {
                                    echo '<i class="text-warning fa fa-star"></i>';
                                } else {
                                    echo '<i class="text-muted fa fa-star"></i>';
                                }
                            }
                            echo '</ul>';
                            if ($isSoldOut) {
                                echo '<p class="text-center mb-0 text-danger">SOLD OUT</p>';
                            } else {
                                echo '<p class="text-center mb-0">' . $row["pret"] . ' RON</p>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No shoes found.</p>';
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Shop Content -->

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
    <script>
        var slider = document.getElementById('slider-range');
        noUiSlider.create(slider, {
            start: [0, 1000],
            connect: true,
            range: {
                'min': 0,
                'max': 1000
            },
            step: 10,
            tooltips: true,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        var valueInput1 = document.getElementById('price-range-value1');
        var valueInput2 = document.getElementById('price-range-value2');

        slider.noUiSlider.on('update', function (values, handle) {
            if (handle) {
                valueInput2.innerHTML = values[handle];
            } else {
                valueInput1.innerHTML = values[handle];
            }

            filterProducts();
        });

        document.querySelectorAll('.size-filter').forEach(function(checkbox) {
            checkbox.addEventListener('change', filterProducts);
        });

        document.querySelectorAll('.brand-filter').forEach(function(checkbox) {
            checkbox.addEventListener('change', filterProducts);
        });

        document.getElementById('sort-az').addEventListener('click', function() {
            var products = Array.from(document.querySelectorAll('.product'));
            products.sort(function(a, b) {
                var nameA = a.getAttribute('data-name').toLowerCase();
                var nameB = b.getAttribute('data-name').toLowerCase();
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0;
            });

            var container = document.getElementById('products');
            container.innerHTML = '';
            products.forEach(function(product) {
                container.appendChild(product);
            });
        });

        function filterProducts() {
            var minPrice = slider.noUiSlider.get()[0];
            var maxPrice = slider.noUiSlider.get()[1];

            var selectedSizes = Array.from(document.querySelectorAll('.size-filter:checked')).map(function(checkbox) {
                return checkbox.value;
            });

            var selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked')).map(function(checkbox) {
                return checkbox.value;
            });

            var products = document.querySelectorAll('.product');
            products.forEach(function (product) {
                var productPrice = parseFloat(product.getAttribute('data-price'));
                var productSizes = product.getAttribute('data-sizes').split(',');
                var productBrand = product.getAttribute('data-brand');

                var matchesPrice = productPrice >= minPrice && productPrice <= maxPrice;
                var matchesSize = selectedSizes.length === 0 || selectedSizes.some(function(size) {
                    return productSizes.includes(size);
                });
                var matchesBrand = selectedBrands.length === 0 || selectedBrands.includes(productBrand);

                if (matchesPrice && matchesSize && matchesBrand) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
    <!-- End Script -->
</body>
</html>