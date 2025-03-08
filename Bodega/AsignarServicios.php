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

// Función para obtener la lista de usuarios activos con roles permitidos
function obtenerUsuariosActivos($pdo) {
    $stmt = $pdo->prepare("
        SELECT a.user_id, a.user_name, u.role 
        FROM active_sessions a
        JOIN users u ON a.user_name = u.name
        WHERE u.role IN ('jefeBodega', 'bodega', 'JefeCedi')
        ORDER BY a.login_time ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para contar facturas asignadas en estado "gestionado"
function contarFacturasGestionadas($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total 
        FROM factura_gestionada fg
        JOIN factura f ON fg.factura_id = f.id
        WHERE fg.user_id = :user_id AND f.estado = 'gestionado'
    ");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchColumn();
}

// Función para obtener facturas pendientes con IntTransaccion
function obtenerFacturasPendientes($pdo, $limite) {
    $stmt = $pdo->prepare("
        SELECT id, IntTransaccion 
        FROM factura 
        WHERE estado = 'pendiente' 
        LIMIT :limite
    ");
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para asignar una factura a un usuario específico
function asignarFactura($pdo, $facturaId, $userId, $userName) {
    $stmt = $pdo->prepare("
        INSERT INTO factura_gestionada (factura_id, user_id, user_name) 
        VALUES (:factura_id, :user_id, :user_name)
    ");
    $stmt->execute([
        'factura_id' => $facturaId,
        'user_id' => $userId,
        'user_name' => $userName
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO estado (factura_id, user_id, user_name, estado) 
        VALUES (:factura_id, :user_id, :user_name, 'gestionado')
    ");
    $stmt->execute([
        'factura_id' => $facturaId,
        'user_id' => $userId,
        'user_name' => $userName
    ]);

    $stmt = $pdo->prepare("
        UPDATE factura 
        SET estado = 'gestionado' 
        WHERE id = :factura_id
    ");
    $stmt->execute(['factura_id' => $facturaId]);
}

// Modificar la función de asignación para filtrar por rol y transacción
function asignarServicios($pdo) {
    $usuarios = obtenerUsuariosActivos($pdo);
    if (empty($usuarios)) {
        $_SESSION['mensaje_servicio'] = "No hay usuarios disponibles para asignar facturas.";
        return;
    }

    $facturas = obtenerFacturasPendientes($pdo, 100); // Obtener un lote de facturas pendientes
    if (empty($facturas)) {
        $_SESSION['mensaje_servicio'] = "No hay facturas pendientes disponibles.";
        return;
    }

    $totalUsuarios = count($usuarios);
    $indiceUsuario = 0; // Iniciar en el primer usuario
    $facturasAsignadas = 0;

    foreach ($facturas as $factura) {
        $intentos = 0; // Contar intentos para evitar bucle infinito
        $asignado = false;

        while (!$asignado && $intentos < $totalUsuarios) {
            $usuario = $usuarios[$indiceUsuario];
            $facturasGestionadas = contarFacturasGestionadas($pdo, $usuario['user_id']);
            $transaccion = $factura['IntTransaccion'];

            // Verificar si el rol del usuario permite esta transacción
            $transaccionesPermitidas = [];
            switch ($usuario['role']) {
                case 'jefeBodega':
                    $transaccionesPermitidas = ['90', '40'];
                    break;
                case 'JefeCedi':
                    $transaccionesPermitidas = ['42', '88'];
                    break;
                case 'bodega':
                    $transaccionesPermitidas = ['90', '40', '42', '88'];
                    break;
            }

            if ($facturasGestionadas < 2 && in_array($transaccion, $transaccionesPermitidas)) {
                // Asignar la factura al usuario
                asignarFactura($pdo, $factura['id'], $usuario['user_id'], $usuario['user_name']);
                $facturasAsignadas++;
                $asignado = true;
            }

            // Avanzar al siguiente usuario
            $indiceUsuario = ($indiceUsuario + 1) % $totalUsuarios;
            $intentos++;
        }

        if (!$asignado) {
            $_SESSION['mensaje_servicio'] = "No hay usuarios disponibles para la transacción {$factura['IntTransaccion']} con menos de 2 facturas gestionadas.";
            break;
        }
    }

    $_SESSION['mensaje_servicio'] = "Se asignaron $facturasAsignadas facturas exitosamente.";
}

// Llamar a la función de asignación
asignarServicios($pdo);

// Consultar las facturas asignadas al usuario en estado 'gestionado'
$stmt = $pdo->prepare("
    SELECT f.IntTransaccion, f.IntDocumento, f.fecha, f.id AS factura_id 
    FROM factura AS f 
    JOIN factura_gestionada AS fg ON f.id = fg.factura_id 
    WHERE fg.user_name = :userName AND f.estado = 'gestionado'
");
$stmt->execute(['userName' => $usuarioConectado]);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Mostrar mensaje de resultado (puedes eliminar esto en producción)
if (isset($_SESSION['mensaje_servicio'])) {
    echo "<p>" . htmlspecialchars($_SESSION['mensaje_servicio']) . "</p>";
    unset($_SESSION['mensaje_servicio']); // Limpiar mensaje después de mostrarlo
}
?>
