<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'admin.php'; // Include your database connection file

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}


// Fetch cart items for the logged-in user
$cart_items = [];
$total_price = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

// Handle item removal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $shoe_id = isset($_POST['shoe_id']) ? (int)$_POST['shoe_id'] : 0;
    
    if ($shoe_id > 0) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // 1. Ștergem din comenzi_detalii
            $stmt = $conn->prepare("DELETE FROM comenzi_detalii 
                                  WHERE shoe_id = ? AND order_id IN 
                                  (SELECT id FROM comenzi WHERE user_id = ? AND status = 'in cart')");
            
            if ($stmt === false) {
                throw new Exception("Eroare la pregătirea query-ului de ștergere: " . $conn->error);
            }
            
            $stmt->bind_param("ii", $shoe_id, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Eroare la executarea ștergerii din comenzi_detalii: " . $stmt->error);
            }
            
            $stmt->close();

            // 2. Actualizăm prețul total în tabela comenzi
            $stmt = $conn->prepare("UPDATE comenzi c 
                                  SET total_price = (
                                      SELECT COALESCE(SUM(cd.quantity * p.pret), 0)
                                      FROM comenzi_detalii cd
                                      JOIN papuci p ON cd.shoe_id = p.id
                                      WHERE cd.order_id = c.id
                                  )
                                  WHERE user_id = ? AND status = 'in cart'");
            
            if ($stmt === false) {
                throw new Exception("Eroare la actualizarea prețului total: " . $conn->error);
            }
            
            $stmt->bind_param("i", $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Eroare la actualizarea prețului: " . $stmt->error);
            }
            
            $stmt->close();

            // 3. Verificăm dacă mai există produse în coș
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM comenzi_detalii cd
                                  JOIN comenzi c ON cd.order_id = c.id
                                  WHERE c.user_id = ? AND c.status = 'in cart'");
            
            if ($stmt === false) {
                throw new Exception("Eroare la verificarea coșului: " . $conn->error);
            }
            
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            // 4. Dacă coșul este gol, ștergem și din comenzi
            if ($row['count'] == 0) {
                $stmt = $conn->prepare("DELETE FROM comenzi 
                                      WHERE user_id = ? AND status = 'in cart'");
                
                if ($stmt === false) {
                    throw new Exception("Eroare la pregătirea ștergerii comenzii: " . $conn->error);
                }
                
                $stmt->bind_param("i", $user_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Eroare la ștergerea comenzii: " . $stmt->error);
                }
                
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();
            
            header('Location: cart.php');
            exit();
            
        } catch (Exception $e) {
            // Rollback în caz de eroare
            $conn->rollback();
            error_log($e->getMessage());
            die("A apărut o eroare la ștergerea produsului.");
        }
    }
}

    // Fetch cart items
    $stmt = $conn->prepare("SELECT p.id as shoe_id, p.nume_papuc, cd.size, cd.quantity, p.pret, 
                          (cd.quantity * p.pret) AS total_price 
                           FROM comenzi_detalii cd 
                           JOIN comenzi c ON cd.order_id = c.id 
                           JOIN papuci p ON cd.shoe_id = p.id 
                           WHERE c.user_id = ? AND c.status = 'in cart'");
    
    if ($stmt === false) {
        error_log("Eroare la pregătirea interogării: " . $conn->error);
        die("A apărut o eroare la procesarea cererii.");
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Eroare la executarea interogării: " . $stmt->error);
        die("A apărut o eroare la procesarea cererii.");
    }
    
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['total_price'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cart - epantofi.ro</title>
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
    <?php include 'header.php'; ?>
    <!-- Close Header -->

    <!-- Cart Content -->
    <div class="container py-5 content">
        <h1 class="h2 pb-4">Coșul de cumpărături</h1>
        <?php if (!empty($cart_items)): ?>
            <ul class="list-group mb-3">
            <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?php echo htmlspecialchars($item['nume_papuc']); ?> 
                            (Mărime: <?php echo htmlspecialchars($item['size']); ?>) 
                            x <?php echo htmlspecialchars($item['quantity']); ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="mx-3"><?php echo htmlspecialchars($item['total_price']); ?> RON</span>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="shoe_id" value="<?php echo $item['shoe_id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger">Sterge</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total</strong>
                    <strong id="total-price"><?php echo $total_price; ?> RON</strong>
                </li>
            </ul>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="20" id="cash-on-delivery">
                <label class="form-check-label" for="cash-on-delivery">
                    Plata cu ramburs (20 RON)
                </label>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <form action="place_order.php" method="post">
                    <input type="hidden" name="total_price" id="final-total-price" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="cash_on_delivery" id="cash-on-delivery-input" value="0">
                    <button type="submit" class="btn btn-primary">Plasează comanda</button>
                </form>
            </div>
        <?php else: ?>
            <p>Coșul de cumpărături este gol.</p>
        <?php endif; ?>
    </div>
    <!-- End Cart Content -->

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
        document.getElementById('cash-on-delivery').addEventListener('change', function() {
            var totalPriceElement = document.getElementById('total-price');
            var finalTotalPriceElement = document.getElementById('final-total-price');
            var cashOnDeliveryInput = document.getElementById('cash-on-delivery-input');
            var totalPrice = parseFloat(finalTotalPriceElement.value);
            if (this.checked) {
                totalPrice += parseFloat(this.value);
                cashOnDeliveryInput.value = "1";
            } else {
                totalPrice -= parseFloat(this.value);
                cashOnDeliveryInput.value = "0";
            }
            totalPriceElement.textContent = totalPrice + ' RON';
            finalTotalPriceElement.value = totalPrice;
        });
    </script>
    <!-- End Script -->
</body>
</html>