<?php
// Verificar si se han enviado los datos a través de la URL
if (isset($_GET['IntTransaccion']) && isset($_GET['IntDocumento'])) {
    $intTransaccion = $_GET['IntTransaccion'];
    $intDocumento = $_GET['IntDocumento'];

    // Crear el nombre del archivo basado en los datos de entrada
    $fileName = 'factura_firmada/' . $intTransaccion . '-' . $intDocumento . '.pdf';

    // Verificar si el archivo existe
    if (file_exists($fileName)) {
        $fileExists = true;
        $fileUrl = $fileName; // URL del archivo para previsualización
    } else {
        $errorMessage = "El archivo no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="p-5 max-w-md mx-auto bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-5">Buscar Factura</h1>
        <form method="GET" action="">
            <div class="mb-4">
                <label for="IntTransaccion" class="block text-sm font-medium text-gray-700">Número de Transacción</label>
                <input type="number" id="IntTransaccion" name="IntTransaccion" required class="mt-1 p-2 w-full border rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="IntDocumento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                <input type="number" id="IntDocumento" name="IntDocumento" required class="mt-1 p-2 w-full border rounded-md shadow-sm">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Buscar</button>
        </form>

        <?php if (isset($errorMessage)): ?>
            <div class="mt-4 text-red-600">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php elseif (isset($fileExists) && $fileExists): ?>
            <div class="mt-4">
                <h2 class="text-lg font-semibold">Previsualización de la Factura:</h2>
                <iframe src="<?= htmlspecialchars($fileUrl) ?>" width="100%" height="500px"></iframe>
                <div class="mt-4">
                    <a href="<?= htmlspecialchars($fileUrl) ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Descargar PDF</a>
                    <a href="https://api.whatsapp.com/send?text=He%20encontrado%20una%20factura%20que%20quiero%20compartir:%20<?= urlencode($fileUrl) ?>" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">Enviar a WhatsApp</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>