<?php
include('../php/db.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($codigo) || empty($descripcion)) {
        die("Error: Código y descripción son obligatorios.");
    }

    // Definir la carpeta donde se guardarán las imágenes de este producto
    $uploadDir = '../Catalogo/fotos' . $codigo . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imagenesGuardadas = [];

    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        $fileName = time() . '_' . basename($_FILES['imagenes']['name'][$key]); // Agrega un timestamp
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($tmp_name, $filePath)) {
            $imagenesGuardadas[] = $filePath;
        } else {
            die("Error al subir la imagen $fileName.");
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO productos (codigo, descripcion, carpeta_imagenes) VALUES (:codigo, :descripcion, :carpeta)");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':carpeta', $uploadDir);
        $stmt->execute();

        echo "Producto guardado correctamente.";
    } catch (PDOException $e) {
        die("Error al guardar el producto: " . $e->getMessage());
    }
}
?>