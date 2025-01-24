<?php
session_start();
include('db.php');

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
    // Obtener el ID de sesión actual
    $session_id = session_id();

    // Eliminar la sesión activa de la base de datos
    $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = ?");
    $stmt->execute([$session_id]);

    // Destruir la sesión de PHP
    session_unset(); // Eliminar todas las variables de sesión
    session_destroy(); // Destruir la sesión actual
}

// Redirigir al formulario de login
header("Location: ../index.php");
exit;