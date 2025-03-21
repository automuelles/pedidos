<?php
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
// Conexión a SQL Server
$serverName = "SERVAUTOMUELLES\SQLDEVELOPER";
$connectionOptions = array(
    "Database" => "AutomuellesDiesel1",
    "Uid" => "Hgi",
    "PWD" => "Hgi"
);

// Establecer conexión con PDO
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=AutomuellesDiesel1", $connectionOptions["Uid"], $connectionOptions["PWD"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>