<?php
session_start();

// Validar si el usuario está autenticado
if (isset($_SESSION['user_name'])) {
    // Incluir archivo de conexión
    include('db.php');
    
    try {
        // Conectar a la base de datos
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Eliminar la sesión del usuario en la tabla active_sessions
        $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE user_name = :user_name");
        $stmt->bindParam(':user_name', $_SESSION['user_name']);
        $stmt->execute();
        
        // Destruir la sesión
        session_unset();
        session_destroy();

        // Redirigir a la página de inicio
        header("Location: ../index.php"); // Cambia esta URL según sea necesario
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirigir a la página de login si no está autenticado
    header("Location: ../index.php");
    exit();
}
?>