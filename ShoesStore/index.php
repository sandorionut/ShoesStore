<?php
include 'admin.php'; // Include your database connection file

// Fetch top 3 best selling shoes
$best_sellers = [];
$stmt = $conn->prepare("SELECT p.id, p.nume_papuc, p.pret, p.imagine, p.descriere, p.rating, SUM(cd.quantity) as total_sales
                        FROM papuci p
                        JOIN comenzi_detalii cd ON p.id = cd.shoe_id
                        GROUP BY p.id, p.nume_papuc, p.pret, p.imagine, p.descriere, p.rating
                        ORDER BY total_sales DESC
                        LIMIT 3");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $best_sellers[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - epantofi.ro</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Close Header -->

    <!-- Home Page Introduction -->
    <div id="template-mo-zay-hero-carousel">
        <div class="container">
            <div class="row p-5">
                <div class="mx-auto col-md-8 col-lg-6 order-lg-last">
                    <img class="img-fluid" src="./assets/img/home_page_shoe_2.jpg" alt="">
                </div>
                <div class="col-lg-6 mb-0 d-flex align-items-center">
                    <div class="text-align-left align-self-center">
                        <h1 class="h1 text-success"><b>epantofi.ro</b></h1>
                        <p>
                            Vezi oferta magazinului epantofi.ro ⭐ Peste 450 de mărci ✔ 50.000 de modele de pantofi, 
                            genți și accesorii ✔ Livrare și returnare gratuită 
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Home Page Introduction -->

    <!-- Best Sellers Shoes -->
    <section class="bg-light">
        <div class="container py-5">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Best Sellers</h1>
                </div>
            </div>
            <div class="row">
                <?php foreach ($best_sellers as $shoe): ?>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="./assets/img/<?php echo htmlspecialchars($shoe['imagine']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($shoe['nume_papuc']); ?>">
                            <div class="card-body">
                                <ul class="list-unstyled d-flex justify-content-between">
                                    <li>
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <?php if ($i < $shoe['rating']): ?>
                                                <i class="text-warning fa fa-star"></i>
                                            <?php else: ?>
                                                <i class="text-muted fa fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </li>
                                    <li class="text-muted text-right"><?php echo htmlspecialchars($shoe['pret']); ?> RON</li>
                                </ul>
                                <a href="shop-single.php?id=<?php echo htmlspecialchars($shoe['id']); ?>" class="h2 text-decoration-none text-dark"><?php echo htmlspecialchars($shoe['nume_papuc']); ?></a>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($shoe['descriere']); ?>
                                </p>
                                <p class="text-muted">Reviews (<?php echo htmlspecialchars($shoe['rating']); ?>)</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Best Sellers Shoes -->

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
    <!-- End Script -->
</body>
</html>