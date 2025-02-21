<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $tercero = $_POST['tercero'];
    $transaccion = $_POST['transaccion'];
    $documento = $_POST['documento'];
    $producto = $_POST['producto'];
    $asesor = $_POST['asesor'];
    $motivo = $_POST['motivo'];
    $usuario = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Desconocido';

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO notas (tercero, transaccion, documento, producto, motivo, usuario, estado) 
            VALUES (:tercero, :transaccion, :documento, :producto, :motivo, :usuario, 'sin gestión')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tercero', $tercero);
    $stmt->bindParam(':transaccion', $transaccion);
    $stmt->bindParam(':documento', $documento);
    $stmt->bindParam(':producto', $producto);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->bindParam(':usuario', $usuario);

    if ($stmt->execute()) {
        // Mostrar alerta y redirigir usando JavaScript
        echo "<script>
                alert('Reporte guardado con éxito.');
                window.location.href = 'notas.php';
              </script>";
        exit(); // Terminar el script
    } else {
        echo "Error al guardar el reporte.";
    }
}
?>