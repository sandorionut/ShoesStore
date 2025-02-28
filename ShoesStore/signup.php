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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verificare dacă parolele coincid
    if ($password !== $confirmPassword) {
        die("Parolele nu coincid!");
    }

    // Verificare dacă email-ul există deja în baza de date
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    if ($stmt === false) {
        die("Eroare la pregătirea interogării: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if ($stmt->execute() === false) {
        die("Eroare la executarea interogării: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        die("Eroare la obținerea rezultatului: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        die("Email-ul există deja în baza de date!");
    }

    $stmt->close();

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO user (nume, prenume, email, parola) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Eroare la pregătirea interogării: " . $conn->error);
    }

    $stmt->bind_param("ssss", $lastName, $firstName, $email, $password);
    if ($stmt->execute() === false) {
        die("Eroare la executarea interogării: " . $stmt->error);
    }

    // Redirect to login page after successful registration
    header("Location: login.html");
    exit();

    $stmt->close();
}

// Închidere conexiune
$conn->close();
?>