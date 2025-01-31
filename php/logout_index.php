<?php
session_start();
include('db.php');

// Verificar si el usuario tiene privilegios de administrador
if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'jefeBodega') {
    die("Acceso denegado.");
}

// Verificar si se envió un session_id
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['session_id'])) {
    $session_id = $_POST['session_id'];

    // Eliminar la sesión activa de la tabla active_sessions
    $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = ?");
    $stmt->execute([$session_id]);

    // Redirigir a la página de gestión de sesiones
    header("Location: ../index.php");
    exit;
} 