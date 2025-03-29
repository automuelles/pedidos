<?php
// Configurar la zona horaria de Colombia
date_default_timezone_set('America/Bogota');
// Verificar si el usuario está conectado y tiene el rol adecuado
if (!isset($_SESSION['user_name']) || 
    (!in_array($_SESSION['user_role'], ['jefeBodega', 'bodega', 'despachos', 'JefeCedi']))) {
    die("Acceso denegado: el usuario no tiene el rol adecuado.");
}

// Datos del usuario conectado
$usuarioConectado = $_SESSION['user_name'];  // Usar el nombre de usuario
$rolUsuario = $_SESSION['user_role'];        // Usar el rol del usuario

// Incluir el archivo de conexión a la base de datos
include('../php/db.php');

try {
    // Crear la tabla factura si no existe
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS factura (
            id INT AUTO_INCREMENT PRIMARY KEY,
            IntTransaccion INT NOT NULL,
            IntDocumento INT NOT NULL,
            StrReferencia1 VARCHAR(255),
            StrReferencia3 VARCHAR(255),
            estado VARCHAR(50) DEFAULT 'pendiente',
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (IntTransaccion, IntDocumento)  -- Garantizar que no se repitan combinaciones
        );
    ";
    $pdo->exec($createTableQuery);

    // Consulta para obtener las facturas de las transacciones 40, 42, 88 y 90 de la fecha actual
    $query = "
        SELECT 
            d.IntTransaccion, 
            d.IntDocumento, 
            doc.StrUsuarioGra, 
            doc.StrReferencia1,
            doc.StrReferencia3
        FROM TblDetalleDocumentos d
        LEFT JOIN TblProductos p ON d.StrProducto = p.StrIdProducto
        LEFT JOIN TblDocumentos doc ON d.IntTransaccion = doc.IntTransaccion AND d.IntDocumento = doc.IntDocumento
        WHERE CONVERT(DATE, d.DatFecha1) = CONVERT(DATE, GETDATE())
        AND d.IntTransaccion IN (40, 42, 88, 90)
        AND d.IntTransaccion >= 0
        AND d.IntDocumento >= 0
        ORDER BY d.IntDocumento
    ";

$mensajero_id = isset($_POST['mensajero']) ? (int) $_POST['mensajero'] : 0;

// Ejecutar la consulta
$stmt = $conn->prepare($query);

    $stmt->execute();
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar la consulta para insertar en la tabla factura
    $insertQuery = "
        INSERT INTO factura (IntTransaccion, IntDocumento, StrReferencia1, StrReferencia3, estado) 
        VALUES (:IntTransaccion, :IntDocumento, :StrReferencia1, :StrReferencia3, 'pendiente')
    ";
    $insertStmt = $pdo->prepare($insertQuery);

    // Insertar cada factura en la tabla factura, filtrando documentos con '-' y evitando duplicados
    foreach ($facturas as $factura) {
        if (strpos((string)$factura['IntDocumento'], '-') === false) {
            // Verificar si la combinación IntTransaccion y IntDocumento ya existe
            $checkQuery = "
                SELECT COUNT(*) FROM factura 
                WHERE IntTransaccion = :IntTransaccion AND IntDocumento = :IntDocumento
            ";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute([
                ':IntTransaccion' => $factura['IntTransaccion'],
                ':IntDocumento' => $factura['IntDocumento']
            ]);
            $exists = $checkStmt->fetchColumn();

            // Si no existe, insertar
            if ($exists == 0) {
                $insertStmt->execute([
                    ':IntTransaccion' => $factura['IntTransaccion'],
                    ':IntDocumento' => $factura['IntDocumento'],
                    ':StrReferencia1' => $factura['StrReferencia1'],
                    ':StrReferencia3' => $factura['StrReferencia3'],
                ]);
            }
        }
    }
    // Insertar en factura_gestionada
    if ($mensajero_id > 0) {
        $sql_insert = "INSERT INTO factura_gestionada (factura_id, user_id, user_name, estado) VALUES (:factura_id, :user_id, :user_name, 'enviado')";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':user_id', $mensajero_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':user_name', $usuarioConectado, PDO::PARAM_STR);
        $stmt_insert->execute();
    }
} catch (PDOException $e) {

    echo "Error al guardar las facturas: " . $e->getMessage();
}
?>
