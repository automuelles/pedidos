<?php
// Incluir la conexión
require_once 'db.php';

// Ejemplo: obtener datos de MySQL
$stmt = $pdo->prepare("SELECT * FROM pedidos");
$stmt->execute();
$pedidos_mysql = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ejemplo: obtener datos de SQL Server
$stmt2 = $conn->prepare("SELECT * FROM pedidos");
$stmt2->execute();
$pedidos_sqlserver = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Devolver datos combinados (o solo uno si quieres)
header('Content-Type: application/json');
echo json_encode([
    'mysql' => $pedidos_mysql,
    'sqlserver' => $pedidos_sqlserver,
]);
?>
