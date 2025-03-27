<?php
// Incluir el archivo de conexión a la base de datos
include('../php/db.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Cargar Producto</h2>
        <form action="procesar.php" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700">Nombre del Producto</label>
                <div class="flex">
                    <input type="text" id="nombre" name="nombre" class="w-full p-2 border rounded-lg" readonly required>
                    <button type="button" onclick="openProductWindow()" class="ml-2 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">Buscar</button>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="w-full p-2 border rounded-lg" rows="3" required></textarea>
            </div>
            <div class="mb-4">
                <label for="categoria" class="block text-gray-700">Grupos</label>
                <select id="categoria" name="categoria" class="w-full p-2 border rounded-lg" required>
                </select>
            </div>
            <div class="mb-4">
                <label for="linea" class="block text-gray-700">Línea de Productos</label>
                <select id="linea" name="linea" class="w-full p-2 border rounded-lg" required>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Cargar Producto</button>
        </form>
    </div>

    <script>
        function openProductWindow() {
            window.open('BuscarProductos.php', 'Seleccionar Producto', 'width=600,height=500,scrollbars=yes');
        }

        function setProduct(productName) {
            document.getElementById("nombre").value = productName;
        }
    </script>
</body>

</html>