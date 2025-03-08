<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Verificar si el usuario es 'mensajeria'
if ($_SESSION['user_role'] !== 'mensajeria') {
    die("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $required_fields = ['fecha_revision', 'placa', 'mensajero', 'tipo_identificacion', 'numero_identificacion', 'kilometraje_inicio'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "El campo $field es obligatorio";
        }
    }
    
    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
        exit;
    }

    try {
        // Preparar la consulta SQL
        $sql = "INSERT INTO revision_motocicleta (
            fecha_revision, placa, mensajero, tipo_identificacion, numero_identificacion,
            kilometraje_inicio, salud_cumple, salud_no_cumple, licencia_cumple, licencia_no_cumple,
            soat_cumple, soat_no_cumple, aceite_cumple, aceite_no_cumple, gasolina_cumple,
            gasolina_no_cumple, bateria_cumple, bateria_no_cumple, guaya_cumple, guaya_no_cumple,
            freno_del_cumple, freno_del_no_cumple, freno_tras_cumple, freno_tras_no_cumple,
            llantas_cumple, llantas_no_cumple, manijas_cumple, manijas_no_cumple,
            estribos_cumple, estribos_no_cumple, luces_del_cumple, luces_del_no_cumple,
            luz_freno_cumple, luz_freno_no_cumple, direcc_del_cumple, direcc_del_no_cumple,
            direcc_tras_cumple, direcc_tras_no_cumple, bocina_cumple, bocina_no_cumple,
            espejos_cumple, espejos_no_cumple, carroceria_cumple, carroceria_no_cumple,
            epp_cumple, epp_no_cumple, encendido_cumple, encendido_no_cumple,
            casco_cumple, casco_no_cumple, aseo_cumple, aseo_no_cumple,
            observaciones, usuario_creacion
        ) VALUES (
            :fecha_revision, :placa, :mensajero, :tipo_identificacion, :numero_identificacion,
            :kilometraje_inicio, :salud_cumple, :salud_no_cumple, :licencia_cumple, :licencia_no_cumple,
            :soat_cumple, :soat_no_cumple, :aceite_cumple, :aceite_no_cumple, :gasolina_cumple,
            :gasolina_no_cumple, :bateria_cumple, :bateria_no_cumple, :guaya_cumple, :guaya_no_cumple,
            :freno_del_cumple, :freno_del_no_cumple, :freno_tras_cumple, :freno_tras_no_cumple,
            :llantas_cumple, :llantas_no_cumple, :manijas_cumple, :manijas_no_cumple,
            :estribos_cumple, :estribos_no_cumple, :luces_del_cumple, :luces_del_no_cumple,
            :luz_freno_cumple, :luz_freno_no_cumple, :direcc_del_cumple, :direcc_del_no_cumple,
            :direcc_tras_cumple, :direcc_tras_no_cumple, :bocina_cumple, :bocina_no_cumple,
            :espejos_cumple, :espejos_no_cumple, :carroceria_cumple, :carroceria_no_cumple,
            :epp_cumple, :epp_no_cumple, :encendido_cumple, :encendido_no_cumple,
            :casco_cumple, :casco_no_cumple, :aseo_cumple, :aseo_no_cumple,
            :observaciones, :usuario_creacion
        )";

        $stmt = $pdo->prepare($sql);
        
        // Preparar los parámetros
        $params = [
            ':fecha_revision' => $_POST['fecha_revision'],
            ':placa' => $_POST['placa'],
            ':mensajero' => $_POST['mensajero'],
            ':tipo_identificacion' => $_POST['tipo_identificacion'],
            ':numero_identificacion' => $_POST['numero_identificacion'],
            ':kilometraje_inicio' => $_POST['kilometraje_inicio'],
            ':salud_cumple' => isset($_POST['salud_cumple']) ? 1 : 0,
            ':salud_no_cumple' => isset($_POST['salud_no_cumple']) ? 1 : 0,
            ':licencia_cumple' => isset($_POST['licencia_cumple']) ? 1 : 0,
            ':licencia_no_cumple' => isset($_POST['licencia_no_cumple']) ? 1 : 0,
            ':soat_cumple' => isset($_POST['soat_cumple']) ? 1 : 0,
            ':soat_no_cumple' => isset($_POST['soat_no_cumple']) ? 1 : 0,
            ':aceite_cumple' => isset($_POST['aceite_cumple']) ? 1 : 0,
            ':aceite_no_cumple' => isset($_POST['aceite_no_cumple']) ? 1 : 0,
            ':gasolina_cumple' => isset($_POST['gasolina_cumple']) ? 1 : 0,
            ':gasolina_no_cumple' => isset($_POST['gasolina_no_cumple']) ? 1 : 0,
            ':bateria_cumple' => isset($_POST['bateria_cumple']) ? 1 : 0,
            ':bateria_no_cumple' => isset($_POST['bateria_no_cumple']) ? 1 : 0,
            ':guaya_cumple' => isset($_POST['guaya_cumple']) ? 1 : 0,
            ':guaya_no_cumple' => isset($_POST['guaya_no_cumple']) ? 1 : 0,
            ':freno_del_cumple' => isset($_POST['freno_del_cumple']) ? 1 : 0,
            ':freno_del_no_cumple' => isset($_POST['freno_del_no_cumple']) ? 1 : 0,
            ':freno_tras_cumple' => isset($_POST['freno_tras_cumple']) ? 1 : 0,
            ':freno_tras_no_cumple' => isset($_POST['freno_tras_no_cumple']) ? 1 : 0,
            ':llantas_cumple' => isset($_POST['llantas_cumple']) ? 1 : 0,
            ':llantas_no_cumple' => isset($_POST['llantas_no_cumple']) ? 1 : 0,
            ':manijas_cumple' => isset($_POST['manijas_cumple']) ? 1 : 0,
            ':manijas_no_cumple' => isset($_POST['manijas_no_cumple']) ? 1 : 0,
            ':estribos_cumple' => isset($_POST['estribos_cumple']) ? 1 : 0,
            ':estribos_no_cumple' => isset($_POST['estribos_no_cumple']) ? 1 : 0,
            ':luces_del_cumple' => isset($_POST['luces_del_cumple']) ? 1 : 0,
            ':luces_del_no_cumple' => isset($_POST['luces_del_no_cumple']) ? 1 : 0,
            ':luz_freno_cumple' => isset($_POST['luz_freno_cumple']) ? 1 : 0,
            ':luz_freno_no_cumple' => isset($_POST['luz_freno_no_cumple']) ? 1 : 0,
            ':direcc_del_cumple' => isset($_POST['direcc_del_cumple']) ? 1 : 0,
            ':direcc_del_no_cumple' => isset($_POST['direcc_del_no_cumple']) ? 1 : 0,
            ':direcc_tras_cumple' => isset($_POST['direcc_tras_cumple']) ? 1 : 0,
            ':direcc_tras_no_cumple' => isset($_POST['direcc_tras_no_cumple']) ? 1 : 0,
            ':bocina_cumple' => isset($_POST['bocina_cumple']) ? 1 : 0,
            ':bocina_no_cumple' => isset($_POST['bocina_no_cumple']) ? 1 : 0,
            ':espejos_cumple' => isset($_POST['espejos_cumple']) ? 1 : 0,
            ':espejos_no_cumple' => isset($_POST['espejos_no_cumple']) ? 1 : 0,
            ':carroceria_cumple' => isset($_POST['carroceria_cumple']) ? 1 : 0,
            ':carroceria_no_cumple' => isset($_POST['carroceria_no_cumple']) ? 1 : 0,
            ':epp_cumple' => isset($_POST['epp_cumple']) ? 1 : 0,
            ':epp_no_cumple' => isset($_POST['epp_no_cumple']) ? 1 : 0,
            ':encendido_cumple' => isset($_POST['encendido_cumple']) ? 1 : 0,
            ':encendido_no_cumple' => isset($_POST['encendido_no_cumple']) ? 1 : 0,
            ':casco_cumple' => isset($_POST['casco_cumple']) ? 1 : 0,
            ':casco_no_cumple' => isset($_POST['casco_no_cumple']) ? 1 : 0,
            ':aseo_cumple' => isset($_POST['aseo_cumple']) ? 1 : 0,
            ':aseo_no_cumple' => isset($_POST['aseo_no_cumple']) ? 1 : 0,
            ':observaciones' => $_POST['observaciones'] ?? '',
            ':usuario_creacion' => $_SESSION['user_name']
        ];

        // Ejecutar la consulta
        if ($stmt->execute($params)) {
            echo "<script>alert('Revisión guardada exitosamente'); window.location.href='Mensajeria.php';</script>";
        } else {
            echo "<script>alert('Error al guardar la revisión'); window.history.back();</script>";
        }

        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>