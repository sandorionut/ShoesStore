<?php
include 'D:\AN3_SEM1\IS\XAMP\htdocs\Site_Pantofi\ShoesStore\admin.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shoe_id = $_POST['shoe_id'];
    $nume_papuc = $_POST['nume_papuc'];

    // Verificăm dacă papucul există
    $stmt = $conn->prepare("SELECT id FROM papuci WHERE id = ? AND nume_papuc = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("is", $shoe_id, $nume_papuc);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Ștergem papucul
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM papuci WHERE id = ? AND nume_papuc = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("is", $shoe_id, $nume_papuc);
        $stmt->execute();
        $stmt->close();
        $message = "Shoe deleted successfully.";
    } else {
        $message = "Shoe not found.";
    }

    header('Location: admin_page_2.php?message=' . urlencode($message));
    exit();
}
?>