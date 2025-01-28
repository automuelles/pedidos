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

        // Obtener el user_id y user_name desde la tabla factura_gestionada
        $sql_user_check = "SELECT user_id, user_name FROM factura_gestionada WHERE id = :factura_id";
        $stmt_user_check = $pdo->prepare($sql_user_check);
        $stmt_user_check->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_user_check->execute();
        $user_info = $stmt_user_check->fetch(PDO::FETCH_ASSOC);

        if (!$user_info) {
            echo json_encode(['success' => false, 'message' => 'No se encontró información del usuario asociado a la factura']);
            exit;
        }

        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];

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

        // Insertar la copia en la tabla 'estado'
        $sql_insert_estado = "INSERT INTO estado (factura_id, user_id, estado, user_name) 
            VALUES (:factura_id, :user_id, :estado, :user_name)";
        $stmt_insert_estado = $pdo->prepare($sql_insert_estado);

        // Vincular los parámetros para la inserción en 'estado'
        $stmt_insert_estado->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_insert_estado->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_insert_estado->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_insert_estado->bindParam(':user_name', $user_name, PDO::PARAM_STR);

        // Ejecutar la inserción en la tabla 'estado'
        $stmt_insert_estado->execute();

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