<?php
include('db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener el ID del usuario y el ID de sesión actual
$user_id = $_SESSION['user_id'];
$session_id = session_id();

// Verificar si la sesión existe en la tabla `active_sessions`
$stmt = $pdo->prepare("SELECT * FROM active_sessions WHERE session_id = ? AND user_id = ?");
$stmt->execute([$session_id, $user_id]);
$active_session = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la sesión activa, redirigir al index
if (!$active_session) {
    header("Location: ../index.php");
    exit;
}

// Consultar los datos del usuario en la tabla `users`
$stmt = $pdo->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se obtuvo un resultado
if (!$user) {
    echo "Error: Usuario no encontrado.";
    exit;
}

// Asignar datos del usuario a variables
$username = $user['name'];
$role = $user['role'] ?: 'Sin rol'; // Si el rol está vacío, asignar "Sin rol"

// Generar el mensaje de bienvenida o advertencia
$message = ($role === 'Sin rol' || $role === 'user') 
    ? "Por favor, comunícate con el administrador para asignarte un rol." 
    : "Bienvenido, $username.";