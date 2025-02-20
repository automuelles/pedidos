<?php
session_start(); // Inicia la sesión para acceder a las variables de sesión

// Conexión a la base de datos
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener datos del formulario
        $inttransaccion = $_POST['inttransaccion'];
        $intdocumento = $_POST['intdocumento'];
        $novedad = $_POST['novedad'];
        $descripcion = $_POST['descripcion'];
        $total_recibido = isset($_POST['total_recibido']) ? (float)$_POST['total_recibido'] : 0.00;

        // Verificar si la sesión tiene un usuario registrado
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id']; // Tomar el ID de usuario de la sesión
        } else {
            $user_id = 0; // Valor por defecto si no hay usuario
        }

        // Determinar el estado según la novedad
        if ($novedad == 'pago en efectivo' || $novedad == 'pago en transferencia') {
            $estado = 'gestionado';
        } else {
            $estado = 'saldo pendiente';
        }

        // Insertar en la tabla Reporte_pago
        $sql = "INSERT INTO Reporte_caja (inttransaccion, intdocumento, user_id, estado, novedad, descripcion, total_recibido)
                VALUES (:inttransaccion, :intdocumento, :user_id, :estado, :novedad, :descripcion, :total_recibido)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':inttransaccion', $inttransaccion, PDO::PARAM_INT);
        $stmt->bindParam(':intdocumento', $intdocumento, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':novedad', $novedad, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':total_recibido', $total_recibido, PDO::PARAM_STR);
        $stmt->execute();

        // Mensaje de éxito y redirección
        echo "<script>
                alert('Reporte y nota guardados correctamente.');
                window.location.href = 'ReportesCaja.php'; // Redirige a la página deseada
              </script>";
    }
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
