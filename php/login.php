<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name']) && isset($_POST['password'])) {
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Consultar usuario por email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->execute([$name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña
        if ($user && password_verify($password, $user['password'])) {
            // Verificar si el usuario ya tiene una sesión activa
            $stmt = $pdo->prepare("SELECT * FROM active_sessions WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $activeSession = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($activeSession && !isset($_POST['force_login'])) {
                // Mostrar mensaje para confirmar si desea iniciar sesión
                echo '<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">';
                echo '<div class="bg-white p-8 rounded-lg shadow-lg max-w-md">';
                echo '<h1 class="text-xl font-bold text-gray-800 mb-4">Sesión Activa Detectada</h1>';
                echo "<p class='text-gray-600 mb-6'>Este usuario ya tiene una sesión activa en otro dispositivo. ¿Desea iniciar sesión de todos modos?</p>";
                echo '<form method="POST" class="space-y-4">';
                echo '<input type="hidden" name="name" value="' . htmlspecialchars($name) . '">';
                echo '<input type="hidden" name="password" value="' . htmlspecialchars($password) . '">';
                echo '<input type="hidden" name="force_login" value="1">';
                echo '<button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Iniciar sesión de todos modos</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                exit;
            }
            

            // Si hay una sesión activa y se confirma el inicio de sesión, eliminar la sesión anterior
            if ($activeSession) {
                $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE user_id = ?");
                $stmt->execute([$user['id']]);
            }

            // Establecer variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['active'] = true;

            // Registrar sesión activa
            $session_id = session_id();
            $stmt = $pdo->prepare("INSERT INTO active_sessions (session_id, user_id, user_name) VALUES (?, ?, ?)");
            $stmt->execute([$session_id, $user['id'], $user['name']]);

            // Redirigir según el rol
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($user['role'] === 'jefeBodega' || $user['role'] === 'JefeCedi') {
                header("Location: ../JefeBodega/Bodega.php");
                exit;
            } elseif ($user['role'] === 'tesoreria' || $user['role'] === 'JefeCedi') {
                header("Location: ../Tesoreria/tesoreria.php");
                exit;
            } elseif ($user['role'] === 'bodega') {
                header("Location: ../Bodega/Bodega.php");
                exit;
            } elseif ($user['role'] === 'despachos') {
                header("Location: ../Despachos/Despachos.php");
                exit;
            } elseif ($user['role'] === 'mensajeria') {
                header("Location: ../Mensajeria/RevisionDiaria.php");
                exit;
            } elseif ($user['role'] === 'Vendedor') {
                header("Location: ../Vendedores/Vendedor.php");
                exit;
            }
            elseif ($user['role'] === 'facturacion') {
                header("Location: ../Facturacion/facturacion.php");
                exit;
            }
            elseif ($user['role'] === 'Compras') {
                header("Location: ../Compras/compras.php");
                exit;
            }
             else {
                header("Location: user_dashboard.php");
                exit;
            }
        } else {
            echo "Credenciales incorrectas.";
        }
    }
}

?>