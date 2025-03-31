<?php
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = urldecode($_GET['file']);
    // Construir la ruta absoluta desde la raíz del proyecto
    $filepath = __DIR__ . '/' . $file;

    // Verificar que el archivo existe y es una imagen
    if (file_exists($filepath) && is_file($filepath) && strpos(mime_content_type($filepath), 'image/') === 0) {
        header('Content-Type: ' . mime_content_type($filepath));
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die("Error: El archivo no existe o no es una imagen válida. Ruta intentada: $filepath");
    }
} else {
    die("Error: No se especificó un archivo para descargar.");
}
?>