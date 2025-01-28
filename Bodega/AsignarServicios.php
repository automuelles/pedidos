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

// Función para verificar y asignar servicios
function asignarServicios($pdo, $userId, $usuarioConectado) {
    // Verificar si el usuario tiene servicios en estado 'picking'
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura_gestionada fg
                           JOIN factura f ON f.id = fg.factura_id
                           WHERE fg.user_id = :user_id AND f.estado = 'picking'");
    $stmt->execute(['user_id' => $userId]);
    $cantidadPicking = $stmt->fetchColumn();

    // Verificar si el usuario tiene menos de 2 servicios asignados y si hay servicios en 'picking'
    if ($cantidadPicking >= 2) {
        // Verificar si al menos uno de esos servicios ha cambiado a un estado diferente a 'picking'
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura_gestionada fg
                               JOIN factura f ON f.id = fg.factura_id
                               WHERE fg.user_id = :user_id AND f.estado != 'picking'");
        $stmt->execute(['user_id' => $userId]);
        $cantidadNoPicking = $stmt->fetchColumn();

        // Si tiene menos de 2 servicios que no están en 'picking', puede asignarse un nuevo servicio
        if ($cantidadNoPicking < 2) {
            // Obtener una factura pendiente
            $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' LIMIT 2");
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
                $stmt = $pdo->prepare("INSERT INTO estado (factura_id, user_id, user_name, estado) VALUES (:factura_id, :user_id, :user_name, 'gestionado')");
                $stmt->execute([
                    'factura_id' => $factura['id'],
                    'user_id' => $userId,
                    'user_name' => $usuarioConectado
                ]);

                // Actualizar el estado de la factura a 'gestionado'
                $stmt = $pdo->prepare("UPDATE factura SET estado = 'gestionado' WHERE id = :factura_id");
                $stmt->execute(['factura_id' => $factura['id']]);

                // Guardar mensaje en sesión sin mostrarlo directamente
                $_SESSION['mensaje_servicio'] = "Servicio asignado correctamente.";
            } else {
                $_SESSION['mensaje_servicio'] = "No hay facturas pendientes para asignar.";
            }
        } else {
            $_SESSION['mensaje_servicio'] = "El usuario ya tiene 2 servicios en estado 'picking' y no puede asignarse más.";
        }
    } else {
        // Si el usuario tiene menos de 2 servicios en 'picking', proceder con la asignación
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura_gestionada WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $cantidadServicios = $stmt->fetchColumn();

        if ($cantidadServicios < 2) {
            // Obtener una factura pendiente
            $stmt = $pdo->prepare("SELECT id FROM factura WHERE estado = 'pendiente' LIMIT 2");
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
                $stmt = $pdo->prepare("INSERT INTO estado (factura_id, user_id, user_name, estado) VALUES (:factura_id, :user_id, :user_name, 'gestionado')");
                $stmt->execute([
                    'factura_id' => $factura['id'],
                    'user_id' => $userId,
                    'user_name' => $usuarioConectado
                ]);

                // Actualizar el estado de la factura a 'gestionado'
                $stmt = $pdo->prepare("UPDATE factura SET estado = 'gestionado' WHERE id = :factura_id");
                $stmt->execute(['factura_id' => $factura['id']]);

                // Guardar mensaje en sesión sin mostrarlo directamente
                $_SESSION['mensaje_servicio'] = "Servicio asignado correctamente.";
            } else {
                $_SESSION['mensaje_servicio'] = "No hay facturas pendientes para asignar.";
            }
        } else {
            $_SESSION['mensaje_servicio'] = "El usuario ya tiene 2 servicios asignados.";
        }
    }
}


// Llamar a la función para asignar servicios
asignarServicios($pdo, $userId, $usuarioConectado);

// Consultar las facturas asignadas al usuario y que estén en estado 'gestionado'
$stmt = $pdo->prepare("
    SELECT f.IntTransaccion, f.IntDocumento, f.fecha, f.id AS factura_id
    FROM factura AS f
    JOIN factura_gestionada AS fg ON f.id = fg.factura_id
    JOIN users AS u ON fg.user_id = u.id
    WHERE u.name = :userName AND f.estado = 'gestionado'
");
$stmt->execute(['userName' => $usuarioConectado]);

// Obtener los resultados
$facturas = $stmt->fetchAll();
?>