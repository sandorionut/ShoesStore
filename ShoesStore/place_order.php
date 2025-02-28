<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'admin.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id'];
$total_price = $_POST['total_price'];
$cash_on_delivery = $_POST['cash_on_delivery'];

// Update order status and total price
$stmt = $conn->prepare("UPDATE comenzi SET status = 'ordered', total_price = ? WHERE user_id = ? AND status = 'in cart'");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("di", $total_price, $user_id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    die('Failed to update order status');
}

// Fetch the latest order ID with status 'ordered'
$stmt = $conn->prepare("SELECT id FROM comenzi WHERE user_id = ? AND status = 'ordered' ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $order_id = $row['id'];
} else {
    die('Order not found');
}

// Debugging: Print order ID
echo "Order ID: $order_id<br>";

// Update shoe stock
$stmt = $conn->prepare("SELECT shoe_id, size, quantity FROM comenzi_detalii WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $shoe_id = $row['shoe_id'];
    $size = $row['size'];
    $quantity = $row['quantity'];

    // Determine the column name for the size
    $size_column = 'm' . $size;

    // Debugging: Print values
    echo "Shoe ID: $shoe_id, Size: $size, Quantity: $quantity, Size Column: $size_column<br>";

    // Update stock
    $stmt_update = $conn->prepare("UPDATE papuci SET $size_column = $size_column - ? WHERE id = ?");
    if ($stmt_update === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt_update->bind_param("ii", $quantity, $shoe_id);
    $stmt_update->execute();
    if ($stmt_update->affected_rows === 0) {
        die('Failed to update stock for shoe ID: ' . $shoe_id . ', size: ' . $size);
    }
}

// Display success message and redirect after 2 seconds
echo 'Order placed successfully';
sleep(2);
header('Location: profile.php');
exit();
?>