<?php
require('fpdf/fpdf.php');
require('fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['status' => 'error', 'message' => json_last_error_msg()]));
}

if (!isset($data['signature'])) {
    die(json_encode(['status' => 'error', 'message' => 'No se recibió la firma.']));
}

// Decodificar la imagen de la firma
$signatureData = $data['signature'];
$signatureImage = str_replace(['data:image/png;base64,', ' '], ['', '+'], $signatureData);
$signatureFile = 'factura_firmada/signature.png';
file_put_contents($signatureFile, base64_decode($signatureImage));

// Crear PDF
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

// Cabecera con color
$pdf->SetFillColor(50, 50, 150);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 15, 'COMPROBANTE DE ENTREGA', 0, 1, 'C', true);
$pdf->Ln(5);

// Información de la empresa
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, 'Automuelles Diesel SAS', 0, 1, 'C');
$pdf->Cell(0, 7, 'NIT: 811021438-4', 0, 1, 'C');
$pdf->Cell(0, 7, 'Cra 61 # 45-04 Medellin (Antioquia)', 0, 1, 'C');
$pdf->Cell(0, 7, 'Telefono: 4483179', 0, 1, 'C');
$pdf->Cell(0, 7, 'Email: automuellesdiesel@outlook.com', 0, 1, 'C');
$pdf->Ln(10);

// Detalles de la transacción
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Transaccion', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 7, 'Transaccion:', 1);
$pdf->Cell(0, 7, $data['IntTransaccion'], 1, 1);
$pdf->Cell(50, 7, 'Numero de Factura:', 1);
$pdf->Cell(0, 7, $data['IntDocumento'], 1, 1);
$pdf->Ln(5);

// Detalles del cliente
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles del Cliente', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 7, 'Nombre:', 1);
$pdf->Cell(0, 7, $data['StrNombre'], 1, 1);
$pdf->Cell(50, 7, 'Direccion:', 1);
$pdf->Cell(0, 7, $data['StrDireccion'], 1, 1);
$pdf->Cell(50, 7, 'Telefono:', 1);
$pdf->Cell(0, 7, $data['StrTelefono'], 1, 1);
$pdf->Ln(5);

// Detalles de la factura
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Factura', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 7, 'Valor:', 1);
$pdf->Cell(0, 7, $data['IntValor'], 1, 1);
$pdf->Cell(50, 7, 'Subtotal:', 1);
$pdf->Cell(0, 7, $data['IntSubtotal'], 1, 1);
$pdf->Cell(50, 7, 'IVA:', 1);
$pdf->Cell(0, 7, $data['IntIva'], 1, 1);
$pdf->Cell(50, 7, 'Total:', 1);
$pdf->Cell(0, 7, $data['IntTotal'], 1, 1);
$pdf->Ln(5);
// Sección para Detalles de Productos
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12); // Establecer la fuente en negrita
$pdf->Cell(0, 10, 'Detalles de Productos', 1, 1, 'C', true);
$pdf->SetFont('Arial', 'B', 12); // Asegurarse de que la fuente esté en negrita para los encabezados
$pdf->Cell(50, 7, 'Producto', 1);
$pdf->Cell(90, 7, 'Descripcion', 1);
$pdf->Cell(50, 7, 'Cantidad', 1);
$pdf->Ln(7); // Salto de línea después del encabezado

// Cambiar a fuente normal para los ítems
$pdf->SetFont('Arial', '', 12);

// Agregar los ítems al PDF
if (!empty($data['items'])) { // Verificar si hay ítems
    foreach ($data['items'] as $item) {
        if (!empty($item['StrProducto'])) {
            // Guardar la posición actual
            $y = $pdf->GetY();
            $pdf->Cell(50, 7, $item['StrProducto'], 1);
            
            // Usar MultiCell para la descripción
            $pdf->SetXY(60, $y); // Cambiar la posición X para la descripción
            $pdf->MultiCell(90, 7, $item['StrDescripcion'], 1);
            
            // Volver a la posición original para la cantidad
            $pdf->SetXY(150, $y); // Cambiar la posición X para la cantidad
            $pdf->Cell(50, 7, number_format($item['IntCantidad'], 2), 1);
            $pdf->Ln(); // Salto de línea después de cada ítem
        }
    }
} else {
    // Si no hay ítems, mostrar un mensaje
    $pdf->Cell(0, 7, 'No hay productos disponibles.', 1, 1, 'C');
}

$pdf->Ln(5);
// Sección para la firma
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Firma del Cliente:', 0, 1);
$pdf->Rect(10, $pdf->GetY(), 80, 40); // Dibujar un cuadro para la firma
$pdf->Image($signatureFile, 12, $pdf->GetY() + 2, 76, 36, 'PNG');
$pdf->Ln(45);
// Guardar el PDF
$pdfFilePath = 'factura_firmada/' . $data['IntTransaccion'] . '-' . $data['IntDocumento'] . '.pdf';
$pdf->Output('F', $pdfFilePath);

// Responder con JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'file' => $pdfFilePath]);
?>