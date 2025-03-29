<?php
// Incluir el archivo de conexión a la base de datos
include('../php/db.php');

try {
    $stmt = $conn->query("SELECT StrIdProducto, StrDescripcion FROM TblProductos ORDER BY StrDescripcion ASC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-3/4">
        <h2 class="text-xl font-bold mb-4 text-center">Seleccionar Producto</h2>

        <!-- Contenedor de filtros aplicados -->
        <div id="filter-container" class="mb-4 flex flex-wrap gap-2"></div>

        <input type="text" id="search" class="w-full p-2 mb-4 border rounded-lg" placeholder="Buscar por código o nombre...">

        <div class="overflow-y-auto h-64 border rounded-lg">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Código</th>
                        <th class="border border-gray-300 p-2">Producto</th>
                        <th class="border border-gray-300 p-2">Acción</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    <?php foreach ($productos as $producto) : ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($producto['StrIdProducto']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($producto['StrDescripcion']); ?></td>
                            <td class="border border-gray-300 p-2 text-center">
                                <button type="button" class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600"
                                    onclick="selectProduct('<?php echo htmlspecialchars($producto['StrIdProducto']); ?>', '<?php echo htmlspecialchars($producto['StrDescripcion']); ?>')">Seleccionar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <button onclick="window.close()" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Cerrar</button>
    </div>

    <script>
        let searchKeywords = [];

        document.getElementById("search").addEventListener("keyup", function(event) {
            let filterText = this.value.trim().toLowerCase();

            if (event.key === "F4" || event.key === "Enter") {
                event.preventDefault();
                if (filterText && !searchKeywords.includes(filterText)) {
                    searchKeywords.push(filterText);
                    this.value = "";
                    updateFilterDisplay();
                }
                filterTable();
            } else if (event.key === "Escape") {
                searchKeywords = [];
                this.value = "";
                updateFilterDisplay();
                filterTable();
            }
        });

        function filterTable() {
            let rows = document.querySelectorAll("#product-list tr");

            rows.forEach(row => {
                let codigo = row.cells[0].textContent.toLowerCase();
                let descripcion = row.cells[1].textContent.toLowerCase();
                let isVisible = searchKeywords.every(keyword => codigo.includes(keyword) || descripcion.includes(keyword));
                row.style.display = isVisible ? "" : "none";
            });
        }

        function updateFilterDisplay() {
            let filterContainer = document.getElementById("filter-container");
            filterContainer.innerHTML = "";

            searchKeywords.forEach((keyword, index) => {
                let filterTag = document.createElement("span");
                filterTag.className = "bg-blue-500 text-white px-3 py-1 rounded-lg text-sm flex items-center gap-2";
                filterTag.innerHTML = `${keyword} <button onclick="removeFilter(${index})" class="ml-2 bg-red-500 text-white rounded-full px-2">✕</button>`;
                filterContainer.appendChild(filterTag);
            });
        }

        function removeFilter(index) {
            searchKeywords.splice(index, 1);
            updateFilterDisplay();
            filterTable();
        }

        function selectProduct(codigo, productName) {
            if (window.opener && !window.opener.closed) {
                window.opener.document.getElementById("nombre").value = codigo;
                window.opener.document.getElementById("descripcion").value = productName;
                window.close();
            }
        }
    </script>
</body>

</html>