<?php
session_start();
require_once('fpdi/src/autoload.php');
require_once('fpdf/fpdf.php');
use setasign\Fpdi\Fpdi;

// Parámetros de conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'automuelles_db';

// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function obtenerNombreDeUsuario()
{
    return isset($_SESSION['usuario']) ? strtoupper($_SESSION['usuario']) : "ADMIN";
}

$response = ['signedPdfUrl' => false];

if (isset($_POST['signature']) && isset($_GET['numeroFactura'])) {
    $numeroFactura = $_GET['numeroFactura'];
    $firmaImagen = $_POST['signature'];

    // Decodificar la imagen de la firma
    $firmaImagen = str_replace('data:image/png;base64,', '', $firmaImagen);
    $firmaImagen = str_replace(' ', '+', $firmaImagen);
    $firmaImagenData = base64_decode($firmaImagen);

    // Crear la carpeta si no existe
$carpetaFirmas = 'imagenes_firmas';
if (!is_dir($carpetaFirmas)) {
    mkdir($carpetaFirmas, 0755, true);
}

// Guardar la imagen de la firma en la carpeta
$firmaImagenPath = $carpetaFirmas . '/firma_' . time() . '.png';
file_put_contents($firmaImagenPath, $firmaImagenData);

    // Crear el PDF firmado
    $carpetaCompartida = '\\\\SERVAUTOMUELLES\\HgiNetERP\\Temp\\Documentos\\dms\\900950921\\cia1\\emp2\\documentos\\FacturaElectronica\\';
    $archivo = $carpetaCompartida . $numeroFactura . '.pdf';
    $pdfFirmadoPath = $carpetaCompartida . $numeroFactura . '_firmado.pdf';
// Ruta local para guardar el PDF firmado
$carpetaFacturasFirmadas = '../factura_firmada';  
if (!is_dir($carpetaFacturasFirmadas)) {
    mkdir($carpetaFacturasFirmadas, 0755, true);  // Crear la carpeta si no existe
}

// Guardar el PDF firmado en la carpeta local
$pdfFirmadoPath = $carpetaFacturasFirmadas . '/' . $numeroFactura . '_firmado.pdf'; // Ruta de almacenamiento local
    if (file_exists($archivo)) {
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile($archivo);
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId);

        // Agregar la firma en la posición deseada
        $pdf->Image($firmaImagenPath,50, 170, 100); // Ajusta la posición y el tamaño de la firma

        // Agregar el nombre del usuario
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(10, 10);
        $pdf->Write(0, 'Firmado por: ' . obtenerNombreDeUsuario());

        // Guardar el PDF firmado
        $pdf->Output('F', $pdfFirmadoPath);

        // Leer el PDF firmado y codificarlo en base64
        $pdfFirmadoContenido = base64_encode(file_get_contents($pdfFirmadoPath));

        // Guardar el PDF firmado en la base de datos junto con el nombre del usuario
        $nombreUsuario = obtenerNombreDeUsuario();
        $stmt = $conn->prepare("INSERT INTO facturas_firmadas (numero_factura, pdf, usuario) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $numeroFactura, $pdfFirmadoContenido, $nombreUsuario);

        if ($stmt->execute()) {
            $response['signedPdfUrl'] = 'buscar_factura_firmada.php';
        } else {
            $response['error'] = 'Error al guardar la factura firmada en la base de datos.';
        }

        $stmt->close();
    } else {
        $response['error'] = 'Factura no encontrada.';
    }

    // Eliminar el archivo temporal de la firma
    unlink($firmaImagenPath);
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>