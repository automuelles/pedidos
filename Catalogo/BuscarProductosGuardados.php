<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .image-preview {
            transition: transform 0.2s;
        }
        .image-preview:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Buscar Productos</h2>
        
        <!-- Formulario de búsqueda -->
        <form method="GET" class="mb-6">
            <div class="flex space-x-4">
                <input type="text" name="search" class="w-full p-2 border rounded-lg" 
                       placeholder="Buscar por código o descripción" 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Buscar
                </button>
            </div>
        </form>

        <!-- Resultados -->
        <?php
        include('../php/db.php'); // Conexión a la base de datos

        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = "%" . trim($_GET['search']) . "%";
            
            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM productos 
                    WHERE codigo LIKE :search 
                    OR descripcion LIKE :search
                ");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
                $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($productos) > 0) {
                    echo '<div class="space-y-4">';
                    foreach ($productos as $producto) {
                        $imagenes = json_decode($producto['carpeta_imagenes'], true);
                        ?>
                        <div class="border p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold">Código: <?php echo htmlspecialchars($producto['codigo']); ?></h3>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                </div>
                                <?php if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'GuardarProductos.php') !== false) { ?>
                                    <button onclick="selectProduct('<?php echo htmlspecialchars($producto['codigo']); ?>')" 
                                            class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700">
                                        Seleccionar
                                    </button>
                                <?php } ?>
                            </div>
                            
736                            <!-- Mostrar imágenes -->
                            <?php if (!empty($imagenes) && is_array($imagenes)) { ?>
                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <?php foreach ($imagenes as $imagen) { ?>
                                        <div class="text-center">
                                            <a href="<?php echo htmlspecialchars($imagen); ?>" target="_blank">
                                                <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                                     class="image-preview w-full h-24 object-cover rounded-lg shadow" 
                                                     alt="Imagen de <?php echo htmlspecialchars($producto['codigo']); ?>" 
                                                     onerror="this.src='https://via.placeholder.com/150?text=Imagen+no+disponible';">
                                            </a>
                                            <a href="download.php?file=<?php echo urlencode($imagen); ?>" 
                                               class="block mt-2 bg-blue-600 text-white py-1 rounded-lg hover:bg-blue-700">
                                                Descargar
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <p class="mt-2 text-gray-500">No hay imágenes disponibles.</p>
                            <?php } ?>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    echo '<p class="text-center text-gray-500">No se encontraron productos.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="text-center text-red-500">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo '<p class="text-center text-gray-500">Ingrese un término de búsqueda para comenzar.</p>';
        }
        ?>

    </div>

    <script>
        function selectProduct(codigo) {
            if (window.opener) {
                window.opener.setProduct(codigo);
                window.close();
            }
        }
    </script>
</body>
</html>