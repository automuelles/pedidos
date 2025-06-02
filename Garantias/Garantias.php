<?php
include('../php/login.php');
include('../php/validate_session.php');
require '../php/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal Automuelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Neumorphism effect */
        .neumorphism {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
        }

        .neumorphism-icon {
            box-shadow: 6px 6px 12px #bebebe, -6px -6px 12px #ffffff;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>

            <a href="#" id="openModal" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs">Apps</span>
            </a>
        </div>
    </nav>

    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6 mt-16">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Formulario de Garantias</h1>
    </div>

    <form id="reclamo-form" class="space-y-6" action="Reclamo.php" method="POST" enctype="multipart/form-data">
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nit-cedula">NIT/CÉDULA</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nit-cedula" name="nit-cedula" type="text" placeholder="NIT/CÉDULA" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre-cliente">NOMBRE DEL CLIENTE</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nombre-cliente" name="nombre-cliente" type="text" placeholder="Nombre del cliente" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="vendedor">VENDEDOR</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vendedor" name="vendedor" required>
                <option value="" disabled selected>Seleccione un vendedor</option>
                <option value="Farley Quiroz">Farley Quiroz</option>
                <option value="Farley Quiroz">Jhonathan Jimenez</option>
                <option value="Farley Quiroz">Norman Calle</option>
                <option value="Abelardo Jimenez">Abelardo Jimenez</option>
                <option value="Roys Ruiz">Roys Ruiz</option>
                <option value="Cristian Cañas">Cristian Cañas</option>
                <option value="Juan Jimenez">Juan Jimenez</option>
                <option value="Juan Roldan">Juan Roldan</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="referencia-producto">REFERENCIA O DESCRIPCIÓN DEL PRODUCTO</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="referencia-producto" name="referencia-producto" type="text" placeholder="Referencia o descripción del producto" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha-instalacion">FECHA DE INSTALACIÓN</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fecha-instalacion" name="fecha-instalacion" type="date" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha-fallo">FECHA DEL FALLO DE LA PIEZA</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fecha-fallo" name="fecha-fallo" type="date" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="marca-vehiculo">MARCA VEHÍCULO LINEA</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="marca-vehiculo" name="marca-vehiculo" type="text" placeholder="Marca Vehículo LINEA" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="modelo-vehiculo">MODELO VEHÍCULO</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="modelo-vehiculo" name="modelo-vehiculo" type="text" placeholder="Modelo vehículo" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="chasis">NÚMERO DE CHASIS</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="chasis" name="chasis" type="text" placeholder="# Chasis" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="vin">NÚMERO VIN</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vin" name="vin" type="text" placeholder="# VIN" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="motor">NÚMERO MOTOR</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="motor" name="motor" type="text" placeholder="# Motor" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="kms-desplazados">KMS DESPLAZADOS</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="kms-desplazados" name="kms-desplazados" type="number" placeholder="Kms desplazados" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tipo-terreno">TIPO DE TERRENO DE DESPLAZAMIENTO</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="tipo-terreno" name="tipo-terreno" type="text" placeholder="Tipo de Terreno de desplazamiento" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="detalle-falla">DETALLE ESPECÍFICO DE LA FALLA</label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="detalle-falla" name="detalle-falla" placeholder="Detalle específico de la falla" rows="4" required></textarea>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="fotos">FOTOS</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fotos" name="fotos[]" type="file" multiple accept="image/*" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="videos">VIDEOS</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="videos" name="videos[]" type="file" multiple accept="video/*">
        </div>

        <div class="flex items-center justify-center">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Enviar</button>
        </div>
    </form>
    </div>

    <script>
    document.getElementById('reclamo-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = event.target;
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        const fotosInput = document.getElementById('fotos');
        let allFilled = true;

        // Validate all required fields except "VIDEOS"
        inputs.forEach(input => {
            if (!input.value.trim() && input.type !== 'file') {
                allFilled = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // Check if at least one file is selected for "FOTOS"
        if (fotosInput.files.length === 0) {
            allFilled = false;
            fotosInput.classList.add('border-red-500');
        } else {
            fotosInput.classList.remove('border-red-500');
        }

        if (!allFilled) {
            alert('Por favor, llena todos los campos obligatorios.');
            return;
        }

        form.submit();
    });
</script>
</body>

</html>