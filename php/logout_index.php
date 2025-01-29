<?php
session_start(); // Inicia la sesión si no está iniciada

// Verificar si la variable de sesión está definida
if (!isset($_SESSION['session_id'])) {
    echo "No hay session_id en la sesión.";
} else {
    require 'db.php'; // Asegurarse de que el archivo de conexión se incluya correctamente
    
    $session_id = $_SESSION['session_id'];
    echo "Session ID: " . $session_id . "<br>"; // Depuración

    $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = :session_id");
    $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Sesión eliminada correctamente de la base de datos.<br>";
    } else {
        echo "No se encontró la sesión en la base de datos.<br>";
    }
    
    // Cerrar conexión PDO
    $stmt = null;
    $pdo = null;
}

// Eliminar variables de sesión
$_SESSION = [];
session_unset();

// Destruir la sesión completamente
session_destroy();

// Eliminar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Redirigir al usuario a la página de inicio
header("Location: ../index.php");
exit();
