<?php
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión MySQL: " . $e->getMessage();
    exit;
}

// Conexión a SQL Server
$serverName = "SERVAUTOMUELLES\SQLDEVELOPER";
$connectionOptions = array(
    "Database" => "AutomuellesDiesel1",
    "Uid" => "Hgi",
    "PWD" => "Hgi"
);
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=" . $connectionOptions["Database"], 
        $connectionOptions["Uid"], 
        $connectionOptions["PWD"]
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión SQL Server: " . $e->getMessage());
}

try {
    // Crear la tabla factura si no existe
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS factura (
            id INT AUTO_INCREMENT PRIMARY KEY,
            IntTransaccion INT NOT NULL,
            IntDocumento INT NOT NULL,
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
            d.IntDocumento
        FROM TblDetalleDocumentos d
        WHERE CONVERT(DATE, d.DatFecha1) = CONVERT(DATE, GETDATE())
        AND d.IntTransaccion IN (40, 42, 88, 90)
        ORDER BY d.IntDocumento
    ";

    // Ejecutar la consulta
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar la consulta para insertar en la tabla factura
    $insertQuery = "
        INSERT INTO factura (IntTransaccion, IntDocumento, estado) 
        VALUES (:IntTransaccion, :IntDocumento, 'pendiente')
    ";
    $insertStmt = $pdo->prepare($insertQuery);

    // Insertar cada factura en la tabla factura, filtrando documentos con '-' y evitando duplicados
    foreach ($facturas as $factura) {
        if (strpos((string)$factura['IntDocumento'], '-') === false) {
            // Verificar si la combinación IntTransaccion e IntDocumento ya existe
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
                    ':IntDocumento' => $factura['IntDocumento']
                ]);
            }
        }
    }
} catch (PDOException $e) {
    echo "Error al guardar las facturas: " . $e->getMessage();
}
?>