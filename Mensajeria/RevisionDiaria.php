<?php
date_default_timezone_set('America/Bogota');
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Verificar si el usuario es 'mensajeria'
if ($_SESSION['user_role'] !== 'mensajeria') {
    die("Acceso denegado.");
}

$user_name = $_SESSION['user_name'];

// Obtener la fecha actual
$current_date = date('Y-m-d');
// Obtener el día de la semana (1 = lunes, 7 = domingo)
$day_of_week = date('N');

// Verificar si ya existe un registro para el usuario en la fecha actual
try {
    $sql = "SELECT COUNT(*) FROM revision_motocicleta WHERE usuario_creacion = :usuario_creacion AND DATE(fecha_revision) = :fecha_revision";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_creacion' => $user_name,
        ':fecha_revision' => $current_date
    ]);
    $count = $stmt->fetchColumn();

    // Si ya existe un registro, redirigir a mensajeria.php
    if ($count > 0) {
        header("Location: mensajeria.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error al verificar el registro: " . $e->getMessage());
}
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
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Mensajeria</h1>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
        <h1 class="text-2xl font-bold text-center mb-6">Formulario de Revisión de Motocicleta</h1>
        <form action="Guardar_revision.php" method="POST">
            <!-- Datos iniciales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de la revisión</label>
                    <input type="date" name="fecha_revision" value="<?php echo $current_date; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required readonly>
                </div>
                <div>
                    <label for="placa" class="block text-sm font-medium text-gray-700">Placa de la motocicleta</label>
                    <select id="placa" name="placa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="">Seleccione una placa</option>
                        <option value="Syt10g">SYT10G</option>
                        <option value="Sxn62G">SXN62G</option>
                        <option value="EPR27H">EPR27H</option>
                        <option value="EEK41G">EEK41G</option>
                        <option value="IKY72E">IKY72E</option>
                        <option value="YOH80F">YOH80F</option>
                        <option value="LRE92G">LRE92G</option>
                        <option value="AEP699">AEP699</option>
                    </select>
                </div>
                <div>
                    <label for="mensajero" class="block text-sm font-medium text-gray-700">Nombre completo del conductor</label>
                    <select id="mensajero" name="mensajero" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="">Seleccione un mensajero</option>
                        <option value="Juan Pablo Pantoja">Juan Pablo Pantoja</option>
                        <option value="Jorge Pereira">Jorge Pereira</option>
                        <option value="Sebastian Sepúlveda">Sebastian Sepúlveda</option>
                        <option value="Julián David Carvajal Gómez">Julián David Carvajal Gómez</option>
                        <option value="Brayan Mazo">Brayan Mazo</option>
                    </select>
                </div>
                <div>
                    <label for="tipo_identificacion" class="block text-sm font-medium text-gray-700">Tipo de identificación</label>
                    <select id="tipo_identificacion" name="tipo_identificacion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="">Seleccione un tipo de identificación</option>
                        <option value="CC">Cédula de ciudadanía (CC)</option>
                        <option value="TE">Tarjeta de extranjería (TE)</option>
                        <option value="CE">Cédula de extranjería (CE)</option>
                        <option value="PEP">Permiso especial de permanencia (PEP)</option>
                        <option value="PP">Pasaporte (PP)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número de identificación</label>
                    <input type="text" name="numero_identificacion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kilometraje de inicio</label>
                    <input type="number" name="kilometraje_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
            </div>

            <!-- Evaluación -->
            <h2 class="text-xl font-semibold mb-4">Evaluación</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado físico y de salud del conductor</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="salud_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="salud_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Licencia del conductor vigente y activa</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="licencia_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="licencia_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">SOAT, Revisión técnico-mecánica vigente</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="soat_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="soat_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de nivel de aceite</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="aceite_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="aceite_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de nivel de la gasolina</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="gasolina_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="gasolina_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de líquido de batería</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="bateria_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="bateria_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de guaya de freno</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="guaya_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="guaya_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de freno delantero</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="freno_del_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="freno_del_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de freno trasero</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="freno_tras_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="freno_tras_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de llantas - labrado, presión</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="llantas_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="llantas_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de las manijas</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="manijas_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="manijas_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de los estribos</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="estribos_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="estribos_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de luces delanteras (Altas - Bajas)</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="luces_del_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="luces_del_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de luz de freno y de posición</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="luz_freno_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="luz_freno_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de luces direccionales delanteras</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="direcc_del_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="direcc_del_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de luces direccionales traseras</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="direcc_tras_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="direcc_tras_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de bocina</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="bocina_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="bocina_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de los espejos retrovisores</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="espejos_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="espejos_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de carrocería</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="carroceria_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="carroceria_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de los EPP</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="epp_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="epp_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado de botón y pedal de encendido</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="encendido_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="encendido_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Estado del casco</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="casco_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="casco_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex-1 text-sm font-medium text-gray-700">Condiciones de aseo general</span>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="aseo_cumple" class="h-4 w-4 text-blue-600"><span>Cumple</span></label>
                    <label class="flex items-center space-x-2"><input type="checkbox" name="aseo_no_cumple" class="h-4 w-4 text-blue-600"><span>No cumple</span></label>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700">Observaciones generales</label>
                <textarea name="observaciones" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" rows="4" required></textarea>
            </div>

            <!-- Botón de envío -->
            <div class="mb-6 text-center">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Guardar</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript para validar checkboxes y mostrar alerta según el día -->
    <script>
       // Obtener el día de la semana desde PHP
    const dayOfWeek = <?php echo $day_of_week; ?>;
    let mensaje = "";

    // Definir el mensaje según el día de la semana
    switch (dayOfWeek) {
        case 1:
            mensaje = "Hoy es <b style='color:red;'>Lunes</b>: Revisión obligatoria para placas terminadas en <b style='color:red;'>3 y 4</b> (EEK41G)";
            break;
        case 2:
            mensaje = "Hoy es <b style='color:red;'>Martes</b>: Revisión obligatoria para placas terminadas en <b style='color:red;'>2 y 8</b> (EPR27H y YOH80F)";
            break;
        case 3:
            mensaje = "Hoy es <b style='color:red;'>Miércoles</b>: Revisión obligatoria para placas terminadas en <b style='color:red;'>5 y 9</b> (LRE92G y AEP699)";
            break;
        case 4:
            mensaje = "Hoy es <b style='color:red;'>Jueves</b>: Revisión obligatoria para placas terminadas en <b style='color:red;'>1 y 7</b> (SYT10G y IKY72E)";
            break;
        case 5:
            mensaje = "Hoy es <b style='color:red;'>Viernes</b>: Revisión obligatoria para placas terminadas en <b style='color:red;'>0 y 6</b> (SXN62G)";
            break;
        default:
            mensaje = "<b style='color:red;'>Hoy no hay revisiones asignadas</b> (Fin de semana)";
            break;
    }

    // Mostrar alerta con SweetAlert2
    Swal.fire({
        title: "<span style='color:red;'>⚠ PICO Y PLACA ⚠</span>",
        html: mensaje,
        icon: "warning", // Cambia el icono a advertencia
        confirmButtonText: "Entendido",
        width: "550px", // Aumenta el ancho del modal
        padding: "25px",
        background: "#fff5f5", // Fondo con tono rojizo suave
        color: "#D8000C", // Color de texto rojo fuerte
        backdrop: true
    });

        // Validación de checkboxes
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxPairs = [
                ['salud_cumple', 'salud_no_cumple'],
                ['licencia_cumple', 'licencia_no_cumple'],
                ['soat_cumple', 'soat_no_cumple'],
                ['aceite_cumple', 'aceite_no_cumple'],
                ['gasolina_cumple', 'gasolina_no_cumple'],
                ['bateria_cumple', 'bateria_no_cumple'],
                ['guaya_cumple', 'guaya_no_cumple'],
                ['freno_del_cumple', 'freno_del_no_cumple'],
                ['freno_tras_cumple', 'freno_tras_no_cumple'],
                ['llantas_cumple', 'llantas_no_cumple'],
                ['manijas_cumple', 'manijas_no_cumple'],
                ['estribos_cumple', 'estribos_no_cumple'],
                ['luces_del_cumple', 'luces_del_no_cumple'],
                ['luz_freno_cumple', 'luz_freno_no_cumple'],
                ['direcc_del_cumple', 'direcc_del_no_cumple'],
                ['direcc_tras_cumple', 'direcc_tras_no_cumple'],
                ['bocina_cumple', 'bocina_no_cumple'],
                ['espejos_cumple', 'espejos_no_cumple'],
                ['carroceria_cumple', 'carroceria_no_cumple'],
                ['epp_cumple', 'epp_no_cumple'],
                ['encendido_cumple', 'encendido_no_cumple'],
                ['casco_cumple', 'casco_no_cumple'],
                ['aseo_cumple', 'aseo_no_cumple']
            ];

            let valid = true;
            checkboxPairs.forEach(pair => {
                const cumple = document.querySelector(`input[name="${pair[0]}"]`);
                const noCumple = document.querySelector(`input[name="${pair[1]}"]`);
                if (!cumple.checked && !noCumple.checked) {
                    valid = false;
                    alert(`Debe seleccionar al menos una opción (Cumple o No cumple) para: ${cumple.closest('.flex').querySelector('span').textContent}`);
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>