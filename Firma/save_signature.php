<?php
// Conectar a la base de datos
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Verificar si los datos fueron enviados
if (isset($_POST['signature']) && isset($_POST['documentNumber']) && isset($_POST['transactionNumber'])) {
    $signatureDataUrl = $_POST['signature'];
    $documentNumber = $_POST['documentNumber'];
    $transactionNumber = $_POST['transactionNumber'];

    // Eliminar el encabezado de la imagen Base64
    $signatureDataUrl = str_replace('data:image/png;base64,', '', $signatureDataUrl);
    $signatureDataUrl = str_replace(' ', '+', $signatureDataUrl); // Reemplazar espacios por +

    // Decodificar la imagen base64
    $signatureImage = base64_decode($signatureDataUrl);

    // Crear el nombre de archivo usando el número de documento y número de transacción
    $signatureFileName = $documentNumber . '-' . $transactionNumber . '.png';
    $filePath = 'imagenes_firmas/' . $signatureFileName;

    // Guardar la imagen en la carpeta
    if (file_put_contents($filePath, $signatureImage)) {
        // Guardar los datos de la firma en la base de datos
        $stmt = $pdo->prepare("INSERT INTO firmas (nombre, documento, transaccion, archivo) VALUES (:name, :documentNumber, :transactionNumber, :signatureFileName)");
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':documentNumber', $documentNumber);
        $stmt->bindParam(':transactionNumber', $transactionNumber);
        $stmt->bindParam(':signatureFileName', $signatureFileName);

        // Ejecutar la inserción
        if ($stmt->execute()) {
            // Responder con la URL del archivo firmado
            echo json_encode(['signedPdfUrl' => 'ruta/a/la/firma/' . $signatureFileName]);
        } else {
            echo json_encode(['error' => 'Error al guardar la firma en la base de datos.']);
        }
    } else {
        echo json_encode(['error' => 'Error al guardar la imagen de la firma.']);
    }
} else {
    echo json_encode(['error' => 'La firma se guardó correctamente.']);
}

$pdo = null; // Cerrar la conexión PDO

?>
