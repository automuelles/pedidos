<?php
include('../php/db.php'); // Conexión a la base de datos

// Obtener los valores de los parámetros de la URL
$transaccion = isset($_GET['IntTransaccion']) ? (int) $_GET['IntTransaccion'] : 0;
$documento = isset($_GET['IntDocumento']) ? (int) $_GET['IntDocumento'] : 0;

if ($transaccion > 0 && $documento > 0) {
    try {
        // Iniciar la transacción
        $pdo->beginTransaction();
        
        // 1. Actualizar el estado de la factura en la tabla 'factura'
        $sql_factura = "UPDATE factura SET estado = 'Mensajeria' WHERE IntTransaccion = :transaccion AND IntDocumento = :documento";
        $stmt_factura = $pdo->prepare($sql_factura);
        $stmt_factura->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
        $stmt_factura->bindParam(':documento', $documento, PDO::PARAM_INT);
        $stmt_factura->execute();
        
        // 2. Obtener la información de la factura gestionada
        $sql_gestionada = "SELECT * FROM factura_gestionada WHERE factura_id IN (SELECT id FROM factura WHERE IntTransaccion = :transaccion AND IntDocumento = :documento)";
        $stmt_gestionada = $pdo->prepare($sql_gestionada);
        $stmt_gestionada->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
        $stmt_gestionada->bindParam(':documento', $documento, PDO::PARAM_INT);
        $stmt_gestionada->execute();
        
        // 3. Insertar solo la factura gestionada en la tabla 'estado'
        $row = $stmt_gestionada->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $sql_estado = "INSERT INTO estado (factura_id, user_id, estado, fecha, user_name) 
                           VALUES (:factura_id, :user_id, 'Mensajeria', NOW(), :user_name)";
            $stmt_estado = $pdo->prepare($sql_estado);
            $stmt_estado->bindParam(':factura_id', $row['factura_id'], PDO::PARAM_INT);
            $stmt_estado->bindParam(':user_id', $row['user_id'], PDO::PARAM_INT);
            $stmt_estado->bindParam(':user_name', $row['user_name'], PDO::PARAM_STR);
            $stmt_estado->execute();
        }
        
        // 4. Actualizar el estado en la tabla 'factura_gestionada' a 'Despachos'
        $sql_update_gestionada = "UPDATE factura_gestionada SET estado = 'Mensajeria' WHERE factura_id IN (SELECT id FROM factura WHERE IntTransaccion = :transaccion AND IntDocumento = :documento)";
        $stmt_update_gestionada = $pdo->prepare($sql_update_gestionada);
        $stmt_update_gestionada->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
        $stmt_update_gestionada->bindParam(':documento', $documento, PDO::PARAM_INT);
        $stmt_update_gestionada->execute();

        // 5. Confirmar la transacción
        $pdo->commit();
       // Mostrar alerta y redirigir a 'pedidosPendientes.php'
       echo "<script>
       alert('Estado actualizado a \"mensajeria\" en factura y factura gestionada correctamente.');
       window.location.href = 'pedidosPendientes.php';
     </script>";
} catch (Exception $e) {
// En caso de error, revertir la transacción
$pdo->rollBack();
echo "Error: " . $e->getMessage();
}
} else {
echo "ID de transacción o documento inválido.";
}
?>