<?php
include('../php/db.php'); // Asegúrate de que este archivo se está incluyendo bien

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($codigo) || empty($descripcion)) {
        die("Error: Código y descripción son obligatorios.");
    }

    // Sanitizar el nombre del directorio
    $safeCodigo = preg_replace('/[^A-Za-z0-9_-]/', '', $codigo); 
    $uploadDir = __DIR__ . "/fotos/$safeCodigo"; 

    // Crear el directorio si no existe
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die("Error: No se pudo crear el directorio '$uploadDir'.");
        }
    }

    $imagenes = [];

    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['imagenes']['name'][$key]);
        $filePath = "$uploadDir/$fileName"; 

        if (move_uploaded_file($tmp_name, $filePath)) {
            $imagenes[] = "fotos/$safeCodigo/$fileName"; 
        } else {
            die("Error al subir la imagen $fileName.");
        }
    }

    $imagenesJSON = json_encode($imagenes);

    try {
        // IMPORTANTE: Asegúrate de usar el nombre correcto de la tabla en tu base de datos
        $stmt = $pdo->prepare("INSERT INTO Productos (codigo, descripcion, carpeta_imagenes) VALUES (:codigo, :descripcion, :imagenes)");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagenes', $imagenesJSON);
        $stmt->execute();

        // ✅ Mostramos alerta y redirigimos a guardarproductos.php
        echo "<script>
                alert('Producto guardado correctamente.');
                window.location.href = 'GuardarProductos.php';
              </script>";
        exit(); // Detener la ejecución después de la redirección
    } catch (PDOException $e) {
        die("Error al guardar el producto: " . $e->getMessage());
    }
}
?>