<?php
require '../php/db.php';

// Create directories if they don't exist
$photos_dir = "fotos/";
$videos_dir = "videos/";

if (!file_exists($photos_dir)) {
    mkdir($photos_dir, 0777, true);
}
if (!file_exists($videos_dir)) {
    mkdir($videos_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $nit_cedula = filter_input(INPUT_POST, 'nit-cedula', FILTER_SANITIZE_STRING);
    $nombre_cliente = filter_input(INPUT_POST, 'nombre-cliente', FILTER_SANITIZE_STRING);
    $vendedor = filter_input(INPUT_POST, 'vendedor', FILTER_SANITIZE_STRING);
    $referencia_producto = filter_input(INPUT_POST, 'referencia-producto', FILTER_SANITIZE_STRING);
    $fecha_instalacion = filter_input(INPUT_POST, 'fecha-instalacion', FILTER_SANITIZE_STRING);
    $fecha_fallo = filter_input(INPUT_POST, 'fecha-fallo', FILTER_SANITIZE_STRING);
    $marca_vehiculo = filter_input(INPUT_POST, 'marca-vehiculo', FILTER_SANITIZE_STRING);
    $modelo_vehiculo = filter_input(INPUT_POST, 'modelo-vehiculo', FILTER_SANITIZE_STRING);
    $chasis = filter_input(INPUT_POST, 'chasis', FILTER_SANITIZE_STRING);
    $vin = filter_input(INPUT_POST, 'vin', FILTER_SANITIZE_STRING);
    $motor = filter_input(INPUT_POST, 'motor', FILTER_SANITIZE_STRING);
    $kms_desplazados = filter_input(INPUT_POST, 'kms-desplazados', FILTER_SANITIZE_NUMBER_INT);
    $tipo_terreno = filter_input(INPUT_POST, 'tipo-terreno', FILTER_SANITIZE_STRING);
    $detalle_falla = filter_input(INPUT_POST, 'detalle-falla', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (
        empty($nit_cedula) ||
        empty($nombre_cliente) ||
        empty($vendedor) ||
        empty($referencia_producto) ||
        empty($fecha_instalacion) ||
        empty($fecha_fallo) ||
        empty($marca_vehiculo) ||
        empty($modelo_vehiculo) ||
        empty($chasis) ||
        empty($vin) ||
        empty($motor) ||
        empty($kms_desplazados) ||
        empty($tipo_terreno) ||
        empty($detalle_falla)
    ) {
        die("Todos los campos obligatorios deben ser completados.");
    }

    // Validate that at least one photo is uploaded
    if (empty($_FILES['fotos']['name'][0])) {
        die("Debes subir al menos una foto.");
    }

    // Handle file uploads
    $photo_paths = [];
    $video_paths = [];

    // Process photos
    if (!empty($_FILES['fotos']['name'][0])) {
        foreach ($_FILES['fotos']['name'] as $key => $name) {
            if ($_FILES['fotos']['error'][$key] == 0) {
                $tmp_name = $_FILES['fotos']['tmp_name'][$key];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = uniqid() . '.' . $ext;
                $destination = $photos_dir . $new_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $photo_paths[] = $destination;
                }
            }
        }
    }

    // Process videos (optional, not mandatory)
    if (!empty($_FILES['videos']['name'][0])) {
        foreach ($_FILES['videos']['name'] as $key => $name) {
            if ($_FILES['videos']['error'][$key] == 0) {
                $tmp_name = $_FILES['videos']['tmp_name'][$key];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = uniqid() . '.' . $ext;
                $destination = $videos_dir . $new_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $video_paths[] = $destination;
                }
            }
        }
    }
}
try {
    // Insert into reclamos table
    $sql = "INSERT INTO reclamos (
            nit_cedula, nombre_cliente, vendedor, referencia_producto, 
            fecha_instalacion, fecha_fallo, marca_vehiculo, modelo_vehiculo, 
            chasis, vin, motor, kms_desplazados, tipo_terreno, detalle_falla
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nit_cedula,
        $nombre_cliente,
        $vendedor,
        $referencia_producto,
        $fecha_instalacion,
        $fecha_fallo,
        $marca_vehiculo,
        $modelo_vehiculo,
        $chasis,
        $vin,
        $motor,
        $kms_desplazados,
        $tipo_terreno,
        $detalle_falla
    ]);

    $reclamo_id = $pdo->lastInsertId();

    // Insert photos
    foreach ($photo_paths as $path) {
        $sql = "INSERT INTO fotos (reclamo_id, ruta) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$reclamo_id, $path]);
    }

    // Insert videos
    foreach ($video_paths as $path) {
        $sql = "INSERT INTO videos (reclamo_id, ruta) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$reclamo_id, $path]);
    }

      // Insert into estado_reclamo table with default state 'recibido'
      $sql = "INSERT INTO estado_reclamo (reclamo_id, nit_cedula) VALUES (?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$reclamo_id, $nit_cedula]);
      
    // Message and redirection
    echo "Reclamo registrado exitosamente!";
    header("Refresh: 2; url=garantias.php");  // Redirects after 2 seconds

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
