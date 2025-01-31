<?php
// Obtener el número de factura desde el parámetro GET
$numeroFactura = isset($_GET['numeroFactura']) ? $_GET['numeroFactura'] : null;

// Verificar si el número de factura está presente
if ($numeroFactura) {
    // Definir la ruta de la carpeta donde se almacenan los PDFs firmados
    $carpetaFacturasFirmadas = '../factura_firmada';
    
    // Ruta del archivo PDF a mostrar
    $pdfPath = $carpetaFacturasFirmadas . '/' . $numeroFactura . '_firmado.pdf';
    
    // Verificar si el archivo PDF existe
    if (file_exists($pdfPath)) {
        // Configurar las cabeceras para mostrar el PDF en el navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
        readfile($pdfPath);  // Leer y mostrar el archivo PDF
        exit;  // Detener la ejecución después de mostrar el archivo
    } else {
        echo "Factura no encontrada.";
    }
} else {
    echo "Número de factura no especificado.";
}
?>