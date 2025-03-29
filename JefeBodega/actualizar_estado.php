<?php
// Inicia la sesión para acceder a las variables de sesión
session_start();

// Incluir archivo de conexión
include('../php/db.php');

// Comprobar que se han recibido los parámetros correctos
if (isset($_POST['factura_id']) && isset($_POST['estado'])) {
    $factura_id = $_POST['factura_id'];
    $estado = $_POST['estado'];

    // Validar y obtener el nombre del usuario desde la sesión
    if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
        $user_name = $_SESSION['user_name'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }

    try {
        // Obtener los datos de la factura_gestionada que se van a copiar
        $sql_select = "SELECT * FROM factura_gestionada WHERE factura_id = :factura_id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_select->execute();
        $factura_data = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Si no se encontró la factura, lanzar un error
        if (!$factura_data) {
            echo json_encode(['success' => false, 'message' => 'Factura no encontrada']);
            exit;
        }

        // Insertar los datos de factura_gestionada en la tabla estado
        $sql_insert_copy = "INSERT INTO estado (factura_id, user_id, estado, fecha, user_name) 
                            VALUES (:factura_id, :user_id, :estado, NOW(), :user_name)";
        $stmt_insert_copy = $pdo->prepare($sql_insert_copy);
        $stmt_insert_copy->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_insert_copy->bindParam(':user_id', $factura_data['user_id'], PDO::PARAM_INT);
        $stmt_insert_copy->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_insert_copy->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $stmt_insert_copy->execute();

        // Cambiar el estado en la tabla factura_gestionada
        $sql_update_factura_gestionada = "UPDATE factura_gestionada SET estado = :estado WHERE factura_id = :factura_id";
        $stmt_update_factura_gestionada = $pdo->prepare($sql_update_factura_gestionada);
        $stmt_update_factura_gestionada->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_update_factura_gestionada->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_update_factura_gestionada->execute();

        // Cambiar el estado en la tabla factura
        $sql_update_factura = "UPDATE factura SET estado = :estado WHERE id = :factura_id";
        $stmt_update_factura = $pdo->prepare($sql_update_factura);
        $stmt_update_factura->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_update_factura->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_update_factura->execute();

        // Respuesta JSON de éxito
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Si ocurre un error, devolver un mensaje de error
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Si los parámetros no están disponibles, devolver un error
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
}
?>