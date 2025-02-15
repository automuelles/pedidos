<?php
require('fpdf/fpdf.php');
require('fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

// Decodificar la imagen de la firma
$signatureData = $data['signature'];
$signatureImage = str_replace('data:image/png;base64,', '', $signatureData);
$signatureImage = str_replace(' ', '+', $signatureImage);
$signatureFile = 'factura_firmada/signature.png';
file_put_contents($signatureFile, base64_decode($signatureImage));

// Crear un nuevo PDF
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetMargins(20, 20, 20);

// Información de la empresa
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'COMPROBANTE DE ENTREGA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Automuelles Diesel SAS', 0, 1, 'C');
$pdf->Cell(0, 10, 'NIT: 811021438-4', 0, 1, 'C');
$pdf->Cell(0, 10, 'Dirección: Cra 61 # 45-04 Medellin (Antioquia)', 0, 1, 'C');
$pdf->Cell(0, 10, 'Teléfono: 4483179', 0, 1, 'C');
$pdf->Cell(0, 10, 'Email: automuellesdiesel@outlook.com', 0, 1, 'C');
$pdf->Ln(20);

// Detalles de la transacción
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Transacción', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Transacción:', 1);
$pdf->Cell(0, 10, $data['IntTransaccion'], 1, 1);
$pdf->Cell(50, 10, 'Número de Factura:', 1);
$pdf->Cell(0, 10, $data['IntDocumento'], 1, 1);
$pdf->Ln(10);

// Detalles del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles del Cliente', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Nombre:', 1);
$pdf->Cell(0, 10, $data['StrNombre'], 1, 1);
$pdf->Cell(50, 10, 'Dirección:', 1);
$pdf->Cell(0, 10, $data['StrDireccion'], 1, 1);
$pdf->Cell(50, 10, 'Teléfono:', 1);
$pdf->Cell(0, 10, $data['StrTelefono'], 1, 1);
$pdf->Ln(10);

// Detalles de la factura
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Factura', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Valor:', 1);
$pdf->Cell(0, 10, $data['IntValor'], 1, 1);
$pdf->Cell(50, 10, 'Subtotal:', 1);
$pdf->Cell(0, 10, $data['IntSubtotal'], 1, 1);
$pdf->Cell(50, 10, 'IVA:', 1);
$pdf->Cell(0, 10, $data['IntIva'], 1, 1);
$pdf->Cell(50, 10, 'Total:', 1);
$pdf->Cell(0, 10, $data['IntTotal'], 1, 1);
$pdf->Ln(10);

// Listado de ítems
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Listado de Ítems', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Producto', 1);
$pdf->Cell(90, 10, 'Descripción', 1);
$pdf->Cell(50, 10, 'Cantidad', 1);
$pdf->Ln(10);

foreach ($data['items'] as $item) {
    if (!empty($item['StrProducto'])) {
        $pdf->Cell(50, 10, $item['StrProducto'], 1);
        $pdf->Cell(90, 10, $item['StrDescripcion'], 1);
        $pdf->Cell(50, 10, number_format($item['IntCantidad'], 2), 1);
        $pdf->Ln(10);
    }
}
$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Canales autorizados para el pago: solo en las cuentas bancarias:', 0, 1);
$pdf->Cell(0, 10, 'AHORROS BANCOLOMBIA #46257223761', 0, 1);
$pdf->Cell(0, 10, 'CORRIENTE BANCO DE BOGOTA #434380127', 0, 1);
$pdf->Cell(0, 10, 'Enviar soporte al correo: auxiliar.conta@automuellesdiesel.com o al WSP 3184010693', 0, 1);
$pdf->Ln(10);

// Sección para la firma
$pdf->Cell(0, 10, 'Firma del Cliente:', 0, 1);

// Asegúrate de ajustar el tamaño y la posición de la firma
$pdf->Image($signatureFile, 10, $pdf->GetY(), 50); // Ajusta la posición y tamaño de la firma

// Guardar el PDF
$pdfFilePath = 'factura_firmada/factura_' . time() . '.pdf';
$pdf->Output('F', $pdfFilePath);

// Responder con el estado
echo json_encode(['status' => 'success', 'file' => $pdfFilePath]);

?>