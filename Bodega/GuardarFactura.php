<?php

// Configurar la zona horaria de Colombia
date_default_timezone_set('America/Bogota');

// Verificar si el usuario está conectado y tiene el rol adecuado
if (!isset($_SESSION['user_name']) || 
    (!in_array($_SESSION['user_role'], ['jefeBodega', 'bodega', 'JefeCedi']))) {
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

    // Obtener la fecha actual en la zona horaria de Colombia
    $fechaActual = date('Y-m-d');

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
        WHERE DATE(d.DatFecha1) = :fechaActual
        AND d.IntTransaccion IN (40, 42, 88, 90)
        AND d.IntTransaccion >= 0
        AND d.IntDocumento >= 0
        ORDER BY d.IntDocumento
    ";

    // Ejecutar la consulta
    $stmt = $conn->prepare($query);
    $stmt->execute([':fechaActual' => $fechaActual]);
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
} catch (PDOException $e) {
    echo "Error al guardar las facturas: " . $e->getMessage();
}
?>