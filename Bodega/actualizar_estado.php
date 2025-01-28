<?php
// Incluir la conexión a la base de datos
include('../php/db.php'); // Asegúrate de incluir el archivo de conexión

// Obtener los datos enviados por POST
if (isset($_POST['factura_id']) && isset($_POST['estado'])) {
    $factura_id = (int) $_POST['factura_id'];
    $estado = $_POST['estado'];

    try {
        // Comprobar si la factura existe y obtener su estado actual en la tabla 'factura'
        $sql_check = "SELECT estado FROM factura_gestionada WHERE id = :factura_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $factura = $stmt_check->fetch(PDO::FETCH_ASSOC);

        // Si la factura no existe
        if (!$factura) {
            echo json_encode(['success' => false, 'message' => 'Factura no encontrada']);
            exit;
        }

        // Verificar si el estado ya es 'picking'
        if ($factura['estado'] === 'picking') {
            echo json_encode(['success' => false, 'message' => 'El estado ya es "picking".']);
            exit;
        }

        // Actualizar el estado en la tabla 'factura_gestionada' a 'picking'
        $sql_update_factura_gestionada = "UPDATE factura_gestionada SET estado = :estado WHERE id = :factura_id";
        $stmt_update_factura_gestionada = $pdo->prepare($sql_update_factura_gestionada);
        
        // Vincular los parámetros
        $stmt_update_factura_gestionada->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_update_factura_gestionada->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        
        // Ejecutar la consulta para la tabla 'factura_gestionada'
        $stmt_update_factura_gestionada->execute();

        // Actualizar el estado en la tabla 'factura'
        $sql_update_factura = "UPDATE factura SET estado = :estado WHERE id = :factura_id";
        $stmt_update_factura = $pdo->prepare($sql_update_factura);
        
        // Vincular los parámetros
        $stmt_update_factura->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_update_factura->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        
        // Ejecutar la consulta para la tabla 'factura'
        $stmt_update_factura->execute();

        // Verificar si se actualizó correctamente en ambas tablas
        if ($stmt_update_factura_gestionada->rowCount() > 0 || $stmt_update_factura->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se realizó el cambio en ninguna de las tablas']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
}
?>