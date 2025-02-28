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
?>