<?php
include 'C:\xampp\htdocs\Site_Pantofi\ShoesStore\admin.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shoe_id = $_POST['shoe_id'];
    $nume_papuc = $_POST['nume_papuc'];
    $pret = $_POST['pret'];
    $imagine = $_POST['imagine'];
    $descriere = $_POST['descriere'];
    $brand = $_POST['brand'];
    $m38 = $_POST['m38'];
    $m39 = $_POST['m39'];
    $m40 = $_POST['m40'];
    $m41 = $_POST['m41'];
    $m42 = $_POST['m42'];
    $m43 = $_POST['m43'];
    $m44 = $_POST['m44'];
    $m45 = $_POST['m45'];
    $rating = $_POST['rating'];

    $stmt = $conn->prepare("UPDATE papuci SET nume_papuc = ?, pret = ?, imagine = ?, descriere = ?, brand = ?, m38 = ?, m39 = ?, m40 = ?, m41 = ?, m42 = ?, m43 = ?, m44 = ?, m45 = ?, rating = ? WHERE id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ssssssssssssssi", $nume_papuc, $pret, $imagine, $descriere, $brand, $m38, $m39, $m40, $m41, $m42, $m43, $m44, $m45, $rating, $shoe_id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin_page_2.php');
    exit();
}
?>