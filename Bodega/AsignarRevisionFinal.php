<?php
// Incluir el archivo de conexión a la base de datos
include('../php/db.php');

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['user_name']) || ($_SESSION['user_role'] !== 'jefeBodega' && $_SESSION['user_role'] !== 'bodega')) {
    die("Acceso denegado: el usuario no tiene el rol adecuado.");
}

// Datos del usuario conectado
$usuarioConectado = $_SESSION['user_name'];  
$rolUsuario = $_SESSION['user_role'];        
$userId = $_SESSION['user_id'];     

// Función para asignar servicios en estado "picking" y actualizar a "RevisionFinal"
function asignarServicios($pdo, $userId, $usuarioConectado) {
    // Verificar si el usuario ya tiene 2 servicios asignados en estado 'RevisionFinal'
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura_gestionada fg
                           JOIN factura f ON f.id = fg.factura_id
                           WHERE fg.user_id = :user_id AND f.estado = 'RevisionFinal'");
    $stmt->execute(['user_id' => $userId]);
    $cantidadRevisionFinal = $stmt->fetchColumn();

    if ($cantidadRevisionFinal < 2) {
        // Obtener una factura pendiente en estado 'picking'
        $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'picking' LIMIT 1");
        $stmt->execute();
        $factura = $stmt->fetch();

        if ($factura) {
            // Asignar esta factura al usuario
            $stmt = $pdo->prepare("INSERT INTO factura_gestionada (factura_id, user_id, user_name) VALUES (:factura_id, :user_id, :user_name)");
            $stmt->execute([
                'factura_id' => $factura['id'],
                'user_id' => $userId,
                'user_name' => $usuarioConectado  // Guardar el nombre de usuario
            ]);

            // Insertar en la tabla estado
            $stmt = $pdo->prepare("INSERT INTO estado (factura_id, user_id, user_name, estado) VALUES (:factura_id, :user_id, :user_name, 'RevisionFinal')");
            $stmt->execute([
                'factura_id' => $factura['id'],
                'user_id' => $userId,
                'user_name' => $usuarioConectado
            ]);

            // Actualizar el estado de la factura a 'RevisionFinal'
            $stmt = $pdo->prepare("UPDATE factura SET estado = 'RevisionFinal' WHERE id = :factura_id");
            $stmt->execute(['factura_id' => $factura['id']]);

            // Guardar mensaje en sesión
            $_SESSION['mensaje_servicio'] = "Servicio asignado correctamente y actualizado a 'RevisionFinal'.";
        } else {
            $_SESSION['mensaje_servicio'] = "No hay facturas pendientes en estado 'picking' para asignar.";
        }
    } else {
        $_SESSION['mensaje_servicio'] = "El usuario ya tiene 2 servicios en estado 'RevisionFinal'.";
    }
}

// Llamar a la función para asignar servicios
asignarServicios($pdo, $userId, $usuarioConectado);

// Consultar las facturas asignadas al usuario en estado 'RevisionFinal'
$stmt = $pdo->prepare("
    SELECT f.IntTransaccion, f.IntDocumento, f.fecha, f.id AS factura_id
    FROM factura AS f
    JOIN factura_gestionada AS fg ON f.id = fg.factura_id
    JOIN users AS u ON fg.user_id = u.id
    WHERE u.name = :userName AND f.estado = 'RevisionFinal'
");
$stmt->execute(['userName' => $usuarioConectado]);

// Obtener los resultados
$facturas = $stmt->fetchAll();
?>