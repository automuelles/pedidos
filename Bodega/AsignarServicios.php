<?php
// Incluir el archivo de conexión a la base de datos
include('../php/db.php');

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['user_name']) || 
    (!in_array($_SESSION['user_role'], ['jefeBodega', 'bodega', 'JefeCedi']))) {
    die("Acceso denegado: el usuario no tiene el rol adecuado.");
}

// Datos del usuario conectado
$usuarioConectado = $_SESSION['user_name'];  
$rolUsuario = $_SESSION['user_role'];        
$userId = $_SESSION['user_id'];     

// Función para verificar y asignar servicios
function asignarServicios($pdo, $userId, $usuarioConectado, $rolUsuario) {
    // Verificar si el usuario tiene una factura en estado "gestionado"
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura_gestionada fg
                           JOIN factura f ON f.id = fg.factura_id
                           WHERE fg.user_id = :user_id AND f.estado = 'gestionado'");
    $stmt->execute(['user_id' => $userId]);
    $cantidadGestionado = $stmt->fetchColumn();

    // Si no tiene ninguna factura en estado "gestionado", asignar una nueva
    if ($cantidadGestionado == 0) {
        $factura = obtenerFacturaPendiente($pdo, $rolUsuario);
        if ($factura) {
            asignarFactura($pdo, $factura['id'], $userId, $usuarioConectado);
        } else {
            $_SESSION['mensaje_servicio'] = "No hay facturas pendientes disponibles.";
        }
    } else {
        $_SESSION['mensaje_servicio'] = "Debe finalizar la factura en 'gestionado' antes de recibir una nueva.";
    }
}

// Función para obtener una factura pendiente según el rol
function obtenerFacturaPendiente($pdo, $rolUsuario) {
    if ($rolUsuario === 'JefeCedi') {
        $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' AND IntTransaccion IN (88, 42) LIMIT 1");
    } elseif ($rolUsuario === 'jefeBodega') {
        $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' AND IntTransaccion IN (90, 40) LIMIT 1");
    } elseif ($rolUsuario === 'bodega') {
        $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' AND IntTransaccion IN (90, 40, 88, 42) LIMIT 1");
    } else {
        $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' LIMIT 1");
    }
    $stmt->execute();
    return $stmt->fetch();
}

// Función para asignar una factura a un usuario
function asignarFactura($pdo, $facturaId, $userId, $usuarioConectado) {
    $stmt = $pdo->prepare("INSERT INTO factura_gestionada (factura_id, user_id, user_name) VALUES (:factura_id, :user_id, :user_name)");
    $stmt->execute([
        'factura_id' => $facturaId,
        'user_id' => $userId,
        'user_name' => $usuarioConectado
    ]);

    $stmt = $pdo->prepare("INSERT INTO estado (factura_id, user_id, user_name, estado) VALUES (:factura_id, :user_id, :user_name, 'gestionado')");
    $stmt->execute([
        'factura_id' => $facturaId,
        'user_id' => $userId,
        'user_name' => $usuarioConectado
    ]);

    $stmt = $pdo->prepare("UPDATE factura SET estado = 'gestionado' WHERE id = :factura_id");
    $stmt->execute(['factura_id' => $facturaId]);

    $_SESSION['mensaje_servicio'] = "Factura asignada correctamente.";
}

// Llamar a la función de asignación
asignarServicios($pdo, $userId, $usuarioConectado, $rolUsuario);

// Consultar las facturas asignadas al usuario y que estén en estado 'gestionado'
$stmt = $pdo->prepare("
    SELECT f.IntTransaccion, f.IntDocumento, f.fecha, f.id AS factura_id
    FROM factura AS f
    JOIN factura_gestionada AS fg ON f.id = fg.factura_id
    WHERE fg.user_name = :userName AND f.estado = 'gestionado'
");
$stmt->execute(['userName' => $usuarioConectado]);

// Obtener los resultados
$facturas = $stmt->fetchAll();
?>