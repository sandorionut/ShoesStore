<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'admin.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT nume, prenume, email, parola FROM user WHERE id = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $first_name = $row['prenume'];
    $last_name = $row['nume'];
    $user_email = $row['email'];
    $password = $row['parola'];
} else {
    die('User not found');
}
$stmt->close();

// Update user details if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE user SET prenume = ?, nume = ?, email = ?, parola = ? WHERE id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ssssi", $new_first_name, $new_last_name, $new_email, $new_password, $user_id);
    $stmt->execute();
    $stmt->close();

    // Refresh the page to show updated details
    header('Location: profile.php');
    exit();
}

// Fetch orders
$orders = [];
$stmt = $conn->prepare("SELECT c.id AS order_id, c.total_price, c.order_date, p.nume_papuc, cd.size, cd.quantity, p.pret 
                        FROM comenzi c 
                        JOIN comenzi_detalii cd ON c.id = cd.order_id 
                        JOIN papuci p ON cd.shoe_id = p.id 
                        WHERE c.user_id = ? AND c.status = 'ordered' 
                        ORDER BY c.order_date DESC");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['order_date'] = $row['order_date'];
    $orders[$row['order_id']]['total_price'] = $row['total_price'];
    $orders[$row['order_id']]['items'][] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - epantofi.ro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/epantofi_logo.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">

    <script>
        function enableEdit(fieldId) {
            document.getElementById(fieldId).removeAttribute('readonly');
            document.getElementById('done_' + fieldId).style.display = 'inline';
        }
    </script>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Close Header -->

    <div class="container">
        <h1>Profile</h1>
        <form method="post" action="profile.php">
            <div class="form-group">
                <label for="last_name"><strong>Last Name:</strong></label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" readonly>
                <button type="button" class="btn btn-link" onclick="enableEdit('last_name')">Edit</button>
                <button type="submit" class="btn btn-link" name="update_profile" id="done_last_name">Done</button>
            </div>
            <div class="form-group">
                <label for="first_name"><strong>First Name:</strong></label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" readonly>
                <button type="button" class="btn btn-link" onclick="enableEdit('first_name')">Edit</button>
                <button type="submit" class="btn btn-link" name="update_profile" id="done_first_name">Done</button>
            </div>
            <div class="form-group">
                <label for="email"><strong>Email:</strong></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                <button type="button" class="btn btn-link" onclick="enableEdit('email')">Edit</button>
                <button type="submit" class="btn btn-link" name="update_profile" id="done_email">Done</button>
            </div>
            <div class="form-group">
                <label for="password"><strong>Password:</strong></label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" readonly>
                <button type="button" class="btn btn-link" onclick="enableEdit('password')">Edit</button>
                <button type="submit" class="btn btn-link" name="update_profile" id="done_password">Done</button>
            </div>
        </form>
        
        
        <h2>Istoric Comenzi</h2>
        <?php if (!empty($orders)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Comandă</th>
                        <th>Produs</th>
                        <th>Mărime</th>
                        <th>Cantitate</th>
                        <th>Preț Unitar</th>
                        <th>Preț Total</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order_id => $order): ?>
                        <tr>
                            <td rowspan="<?php echo count($order['items']); ?>"><?php echo htmlspecialchars($order_id); ?></td>
                            <td><?php echo htmlspecialchars($order['items'][0]['nume_papuc']); ?></td>
                            <td><?php echo htmlspecialchars($order['items'][0]['size']); ?></td>
                            <td><?php echo htmlspecialchars($order['items'][0]['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($order['items'][0]['pret']); ?> RON</td>
                            <td rowspan="<?php echo count($order['items']); ?>"><?php echo htmlspecialchars($order['total_price']); ?> RON</td>
                            <td rowspan="<?php echo count($order['items']); ?>"><?php echo htmlspecialchars($order['order_date']); ?></td>
                        </tr>
                        <?php for ($i = 1; $i < count($order['items']); $i++): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['items'][$i]['nume_papuc']); ?></td>
                                <td><?php echo htmlspecialchars($order['items'][$i]['size']); ?></td>
                                <td><?php echo htmlspecialchars($order['items'][$i]['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($order['items'][$i]['pret']); ?> RON</td>
                            </tr>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nu aveți comenzi plasate.</p>
        <?php endif; ?>
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
    <script src="assets/js/jquery-1.11.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>