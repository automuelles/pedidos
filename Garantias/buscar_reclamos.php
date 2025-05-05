<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['documento'])) {
    echo json_encode([]);
    exit;
}

$documento = trim($_POST['documento']);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'automuelles_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, nit_cedula FROM reclamos WHERE nit_cedula = ?");
    $stmt->execute([$documento]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode([]);
}