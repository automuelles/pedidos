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

class PDF extends Fpdi
{

    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage('L'); // 'L' para horizontal
$pdf->SetMargins(10, 10, 10);

// Información de la empresa con logo
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 12);

// Agregar la imagen del logo en la parte izquierda (ajustar el tamaño y la posición según sea necesario)
$logoPath = 'logo.png'; // Asegúrate de que la ruta sea correcta
$pdf->Image($logoPath, 10, 10, 50); // 10 de margen izquierdo, 10 de margen superior, 30 de ancho

// Mover la posición a la derecha del logo para la información de la empresa
$pdf->SetXY(45, 10); // Ajustar la posición para que el texto quede alineado correctamente con el logo

// Información de la empresa
$pdf->Cell(0, 7, 'Automuelles Diesel SAS', 0, 1, 'C');
$pdf->SetX(45); // Asegurar que la siguiente línea comience en la misma posición
$pdf->Cell(0, 7, 'NIT: 811021438-4', 0, 1, 'C');
$pdf->SetX(45);
$pdf->Cell(0, 7, 'Direccion: Cra 61 # 45-04 Medellín (Antioquia)', 0, 1, 'C');
$pdf->SetX(45);
$pdf->Cell(0, 7, 'Telefono: 4483179', 0, 1, 'C');
$pdf->SetX(45);
$pdf->Cell(0, 7, 'Email: Facturas.compras@automuellesdiesel.com', 0, 1, 'C');
$pdf->Ln(10);

// Detalles de la transacción
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Transaccion', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 7, 'Transaccion:', 0);
$pdf->Cell(0, 7, $data['IntTransaccion'], 0, 1);
$pdf->Cell(50, 7, 'Numero de Factura:', 0);
$pdf->Cell(0, 7, $data['IntDocumento'], 0, 1);
$pdf->Ln(5);

// Detalles del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 7, 'Nit/Ced:', 0);
$pdf->Cell(0, 7, $data['StrIdTercero'], 0, 1);
$pdf->Cell(50, 7, 'Nombre:', 0);
$pdf->Cell(0, 7, $data['StrNombre'], 0, 1);
$pdf->Cell(50, 7, 'Direccion:', 0);
$pdf->Cell(0, 7, $data['StrDireccion'], 0, 1);
$pdf->Cell(50, 7, 'Telefono:', 0);
$pdf->Cell(0, 7, $data['StrTelefono'], 0, 1);
$pdf->Ln(5);

// Sección para Detalles de Productos
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de Productos', 1,  1, 'C', true);
$pdf->SetFont('Arial', 'B', 12);

// Definir los anchos de las columnas
$widthProducto = 50;       // Ancho para Producto
$widthDescripcion = 152;   // Ancho para Descripción (ajustado)
$widthCantidad = 36;       // Ancho para Cantidad
$widthValorUnitario = 36;  // Ancho para Valor Unitario

// Encabezados de las columnas
$pdf->Cell($widthProducto, 7, 'Producto', 1);
$pdf->Cell($widthDescripcion, 7, 'Descripcion', 1);
$pdf->Cell($widthCantidad, 7, 'Cantidad', 1);
$pdf->Cell($widthValorUnitario, 7, 'Valor Unitario', 1);
$pdf->Ln(); // Salto de línea después del encabezado

$maxDescripcionLength = 60; // Para truncar la descripción si es muy larga

// Cambiar a fuente normal para los ítems
$pdf->SetFont('Arial', '', 12);

foreach ($data['items'] as $item) {
    $pdf->Cell($widthProducto, 7, htmlspecialchars($item['StrProducto']), 1, 0, 'L');
    
    // Truncar la descripción si es muy larga
    $descripcion = htmlspecialchars($item['StrDescripcion']);
    if (strlen($descripcion) > $maxDescripcionLength) {
        $descripcion = substr($descripcion, 0, $maxDescripcionLength) . '...'; // Agregar puntos suspensivos
    }

    $pdf->Cell($widthDescripcion, 7, $descripcion, 1, 0, 'L');
    $pdf->Cell($widthCantidad, 7, number_format($item['IntCantidad'], 2), 1, 0, 'R'); 
    $pdf->Cell($widthValorUnitario, 7, number_format($item['IntValorUnitario'], 2), 1, 1, 'R'); // Agregar celda de Valor Unitario y salto de línea
}
$pdf->Ln(10);

// Detalles de la factura
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 12); // Asegúrate de que la fuente esté en negrita
$pdf->Cell(0, 10, 'Detalles de la Factura', 1, 1, 'C', true);
$pdf->SetFont('Arial', 'B', 12); // Mantener la fuente en negrita para los títulos

// Definir el ancho de las columnas
$widthLabel = 69; // Ancho para las etiquetas
$widthValue = 40; // Ancho para los valores

// Agregar los títulos en forma de columna
$pdf->Cell($widthLabel, 7, 'Valor:', 1);
$pdf->Cell($widthLabel, 7, 'Subtotal:', 1);
$pdf->Cell($widthLabel, 7, 'IVA:', 1);
$pdf->Cell($widthLabel, 7, 'Total:', 1);
$pdf->Ln(); // Salto de línea para la siguiente fila

// Cambiar a fuente normal para los valores
$pdf->SetFont('Arial', '', 12);
// Agregar los datos en forma de fila
$pdf->Cell($widthLabel, 7, number_format($data['IntValor'], 2), 1);
$pdf->Cell($widthLabel, 7, number_format($data['IntSubtotal'], 2), 1);
$pdf->Cell($widthLabel, 7, number_format($data['IntIva'], 2), 1);
$pdf->Cell($widthLabel, 7, number_format($data['IntTotal'], 2), 1);
$pdf->Ln(5); // Salto de línea adicional después de los detalles
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
