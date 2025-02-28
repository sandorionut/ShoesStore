<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'admin.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id'];
$shoe_id = $_POST['shoe_id'];
$size = $_POST['size'];
$quantity = $_POST['quantity'];

// Fetch the price of the shoe
$stmt = $conn->prepare("SELECT pret FROM papuci WHERE id = ?");
$stmt->bind_param("i", $shoe_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $price = $row['pret'];
} else {
    die('Shoe not found');
}

// Calculate total price for the item
$item_total_price = $price * $quantity;

// Check if there is an existing cart for the user
$stmt = $conn->prepare("SELECT id, total_price FROM comenzi WHERE user_id = ? AND status = 'in cart'");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $order_id = $row['id'];
    $current_total_price = $row['total_price'];
} else {
    // Create a new cart for the user
    $stmt = $conn->prepare("INSERT INTO comenzi (user_id, status, total_price) VALUES (?, 'in cart', 0)");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $current_total_price = 0;
}

// Add the item to the cart
$stmt = $conn->prepare("INSERT INTO comenzi_detalii (order_id, shoe_id, size, quantity) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("iisi", $order_id, $shoe_id, $size, $quantity);
$stmt->execute();

// Update the total price of the order
$new_total_price = $current_total_price + $item_total_price;
$stmt = $conn->prepare("UPDATE comenzi SET total_price = ? WHERE id = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("di", $new_total_price, $order_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo 'Item added to cart';
} else {
    echo 'Failed to add item to cart';
}
?>