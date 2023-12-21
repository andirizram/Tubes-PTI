<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your username
$password = "andisa8171"; // Replace with your password
$dbname = "gempabumi"; // Replace with your database name

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Replace with your authentication logic using PDO
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nip = $_POST["username"]; // Assuming the NIP is entered in the "username" field
        $password = $_POST["password"];

        // Add your authentication logic using PDO here
        // Example using prepared statement (replace with your own):
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nip = :nip AND password = :password");
        $stmt->bindParam(':nip', $nip);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Check if a matching user was found
        if ($stmt->rowCount() == 1) {
            $_SESSION["username"] = $nip; // Store NIP in the session
            header("Location: ../pages/index-admin.php"); // Redirect to dashboard.php after successful login
            exit();
        } else {
            echo "Invalid NIP or password"; // Display an error message on the login page
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
