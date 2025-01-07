<?php
// Database credentials
$host = 'localhost';         // Database host (usually localhost)
$dbname = 'zntech';    // Database name
$username = 'root';  // Database username
$password = '';  // Database password

try {
    // Set DSN (Data Source Name) for PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

    // Create a PDO instance
    $pdo = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Uncomment the line below if you want to check the connection
    // echo "Connected successfully";

} catch (PDOException $e) {
    // If the connection fails, display an error message
    die("Connection failed: " . $e->getMessage());
}
?>
