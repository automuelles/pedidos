<?php
// Incluir archivos necesarios
include('../php/login.php');
include('../php/validate_session.php');
include('../php/db.php');

// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener los valores de los parámetros desde el formulario
$transaccion = isset($_POST['IntTransaccion']) ? (int) $_POST['IntTransaccion'] : 0;
$documento = isset($_POST['IntDocumento']) ? (int) $_POST['IntDocumento'] : 0;
$mensajero_id = isset($_POST['mensajero']) ? (int) $_POST['mensajero'] : 0;

// Debugging: Verificar valores recibidos
var_dump($_POST); // Quitar esto después de depurar

// Verificar que los IDs sean válidos
if ($transaccion > 0 && $documento > 0 && $mensajero_id > 0) {
    // Obtener el factura_id desde la tabla factura
    $sql_factura = "SELECT id FROM factura WHERE IntTransaccion = :transaccion AND IntDocumento = :documento";
    $stmt_factura = $pdo->prepare($sql_factura);
    $stmt_factura->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
    $stmt_factura->bindParam(':documento', $documento, PDO::PARAM_INT);
    $stmt_factura->execute();
    $factura = $stmt_factura->fetch(PDO::FETCH_ASSOC);

    if ($factura) {
        $factura_id = $factura['id'];

        // Obtener el nombre del mensajero seleccionado desde la tabla users
        $sql_mensajero = "SELECT name FROM users WHERE id = :mensajero_id";
        $stmt_mensajero = $pdo->prepare($sql_mensajero);
        $stmt_mensajero->bindParam(':mensajero_id', $mensajero_id, PDO::PARAM_INT);
        $stmt_mensajero->execute();
        $mensajero = $stmt_mensajero->fetch(PDO::FETCH_ASSOC);

        if ($mensajero) {
            $mensajero_name = $mensajero['name'];

            // Insertar en la tabla factura_gestionada con el nombre del mensajero
            $sql_gestionada = "INSERT INTO factura_gestionada (factura_id, user_id, user_name, estado) 
                               VALUES (:factura_id, :user_id, :user_name, 'Enviado')";
            $stmt_gestionada = $pdo->prepare($sql_gestionada);
            $stmt_gestionada->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
            $stmt_gestionada->bindParam(':user_id', $mensajero_id, PDO::PARAM_INT);
            $stmt_gestionada->bindParam(':user_name', $mensajero_name, PDO::PARAM_STR); // Nombre del mensajero
            $stmt_gestionada->execute();

            // Insertar en la tabla estado con el nombre del mensajero
            $sql_estado = "INSERT INTO estado (factura_id, user_id, estado, user_name) 
                           VALUES (:factura_id, :user_id, 'Enviado', :user_name)";
            $stmt_estado = $pdo->prepare($sql_estado);
            $stmt_estado->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
            $stmt_estado->bindParam(':user_id', $mensajero_id, PDO::PARAM_INT);
            $stmt_estado->bindParam(':user_name', $mensajero_name, PDO::PARAM_STR); // Nombre del mensajero
            $stmt_estado->execute();

            // Actualizar el estado en la tabla factura a "Enviado"
            $sql_update = "UPDATE factura SET estado = 'Enviado' WHERE id = :factura_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
            $stmt_update->execute();

            // Redirigir o mostrar mensaje de éxito
            header("Location: pedidosPendientes.php"); // Redirige a la página deseada
            exit();
        } else {
            echo "No se encontró el mensajero en la base de datos.";
        }
    } else {
        echo "No se encontró la factura en la base de datos.";
    }
} else {
    echo "ID de transacción, documento o mensajero inválido.";
}
?>