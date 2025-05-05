<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reclamo_id'])) {
    echo json_encode([]);
    exit;
}

$reclamo_id = intval($_POST['reclamo_id']);

// Conexión
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'automuelles_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener estados
    $sql = "SELECT estado, fecha_actualizacion FROM estado_reclamo WHERE reclamo_id = ? ORDER BY fecha_actualizacion ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$reclamo_id]);

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos']);
}
