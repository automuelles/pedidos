<?php
session_start();
require_once '../php/db.php'; // Incluye el archivo de configuración de la base de datos

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene el ID de la factura desde la URL
    $factura_id = isset($_GET['factura_id']) ? (int) $_GET['factura_id'] : 0;

    // Verifica si la sesión está iniciada y si 'user_id' está definido
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        // Si la sesión no está iniciada o 'user_id' no está definido, redirige al usuario
        header('Location: login.php');
        exit;
    }

    $estado = 'sin gestion'; // Valor por defecto
    $fecha = date('Y-m-d H:i:s');
    $novedad = $_POST['novedad'];
    $descripcion = $_POST['descripcion'];

    // Prepara la consulta SQL para insertar los datos
    $sql = "INSERT INTO Novedades_Bodega (factura_id, user_id, estado, fecha, novedad, descripcion)
            VALUES (:factura_id, :user_id, :estado, :fecha, :novedad, :descripcion)";

    // Prepara la declaración
    try {
        $stmt = $pdo->prepare($sql);

        // Vincula los parámetros y ejecuta la declaración
        $stmt->bindValue(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindValue(':novedad', $novedad, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);

        // Ejecuta la declaración
        if ($stmt->execute()) {
            // Si la inserción fue exitosa, muestra una alerta y cierra la ventana
            echo "<script>
                    alert('Se guardó el reporte de forma exitosa.');
                    window.close();
                  </script>";
        } else {
            // Si hay un error en la ejecución, muestra una alerta y cierra la ventana
            echo "<script>
                    alert('No se pudo guardar el reporte.');
                    window.close();
                  </script>";
        }
    } catch (PDOException $e) {
        // Si ocurre un error, muestra una alerta con el mensaje de error y cierra la ventana
        echo "<script>
                alert('Error al guardar el reporte: " . $e->getMessage() . "');
                window.close();
              </script>";
    }
} 
?>