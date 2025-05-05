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
    $fecha_venta = filter_input(INPUT_POST, 'fecha-venta', FILTER_SANITIZE_STRING);
    $referencia_producto = filter_input(INPUT_POST, 'referencia-producto', FILTER_SANITIZE_STRING);
    $fecha_instalacion = filter_input(INPUT_POST, 'fecha-instalacion', FILTER_SANITIZE_STRING);
    $fecha_fallo = filter_input(INPUT_POST, 'fecha-fallo', FILTER_SANITIZE_STRING);
    $tiempo_instalado = filter_input(INPUT_POST, 'tiempo-instalado', FILTER_SANITIZE_STRING);
    $marca_vehiculo = filter_input(INPUT_POST, 'marca-vehiculo', FILTER_SANITIZE_STRING);
    $modelo_vehiculo = filter_input(INPUT_POST, 'modelo-vehiculo', FILTER_SANITIZE_STRING);
    $chasis = filter_input(INPUT_POST, 'chasis', FILTER_SANITIZE_STRING);
    $vin = filter_input(INPUT_POST, 'vin', FILTER_SANITIZE_STRING);
    $motor = filter_input(INPUT_POST, 'motor', FILTER_SANITIZE_STRING);
    $kms_desplazados = filter_input(INPUT_POST, 'kms-desplazados', FILTER_SANITIZE_NUMBER_INT);
    $tipo_terreno = filter_input(INPUT_POST, 'tipo-terreno', FILTER_SANITIZE_STRING);
    $fecha_remocion = filter_input(INPUT_POST, 'fecha-remocion', FILTER_SANITIZE_STRING);
    $detalle_falla = filter_input(INPUT_POST, 'detalle-falla', FILTER_SANITIZE_STRING);

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

    // Process videos
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

    try {
        // Insert into reclamos table
        $sql = "INSERT INTO reclamos (
            nit_cedula, fecha_venta, referencia_producto, fecha_instalacion, 
            fecha_fallo, tiempo_instalado, marca_vehiculo, modelo_vehiculo, 
            chasis, vin, motor, kms_desplazados, tipo_terreno, 
            fecha_remocion, detalle_falla
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nit_cedula,
            $fecha_venta,
            $referencia_producto,
            $fecha_instalacion,
            $fecha_fallo,
            $tiempo_instalado,
            $marca_vehiculo,
            $modelo_vehiculo,
            $chasis,
            $vin,
            $motor,
            $kms_desplazados,
            $tipo_terreno,
            $fecha_remocion,
            $detalle_falla
        ]);

        $reclamo_id = $pdo->lastInsertId();

        // Insertar en estado_reclamo con estado por defecto 'recibido'
        $sql_estado_reclamo = "INSERT INTO estado_reclamo (reclamo_id, nit_cedula, estado) VALUES (?, ?, ?)";
        $stmt_estado_reclamo = $pdo->prepare($sql_estado_reclamo);
        $stmt_estado_reclamo->execute([$reclamo_id, $nit_cedula, 'recibido']);

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

        // Message and redirection
        echo "Reclamo registrado exitosamente!";
        // Redirect to garantias.php after 2 seconds
        header("Refresh: 2; url=garantias.php");  // Redirects after 2 seconds

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
$pdo = null;
