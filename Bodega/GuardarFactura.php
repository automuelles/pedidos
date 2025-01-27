<?php

// Verificar si el usuario está conectado y tiene el rol adecuado
if (!isset($_SESSION['user_name']) || ($_SESSION['user_role'] !== 'jefeBodega' && $_SESSION['user_role'] !== 'bodega')) {
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
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

    // Insertar cada factura en la tabla factura, filtrando documentos con '-'
    foreach ($facturas as $factura) {
        if (strpos((string)$factura['IntDocumento'], '-') === false) {
            $insertStmt->execute([
                ':IntTransaccion' => $factura['IntTransaccion'],
                ':IntDocumento' => $factura['IntDocumento'],
                ':StrReferencia1' => $factura['StrReferencia1'],
                ':StrReferencia3' => $factura['StrReferencia3'],
            ]);
        }
    }

} catch (PDOException $e) {
    echo "Error al guardar las facturas: " . $e->getMessage();
}
?>