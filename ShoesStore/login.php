<?php
session_start();

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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Pregătiți și executați interogarea SQL
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND parola = ?");
    if ($stmt === false) {
        die("Eroare la pregătirea interogării: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    if ($stmt->execute() === false) {
        die("Eroare la executarea interogării: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        die("Eroare la obținerea rezultatului: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        // Login reușit, stocați informațiile utilizatorului în sesiune
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nume'];

        // Redirecționați către pagina principală
        header("Location: index.php");
        exit();
    } else {
        // Email sau parolă incorecte
        echo "Email sau parolă incorecte!";
    }

    $stmt->close();
}

// Închidere conexiune
$conn->close();
?>