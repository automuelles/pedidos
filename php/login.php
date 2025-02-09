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

            if ($activeSession) {
                // Denegar el inicio de sesión si ya está logueado
                echo "Este usuario ya tiene una sesión activa en otro dispositivo.";
            } else {
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
                    exit; // Asegúrate de salir después de redirigir
                } elseif ($user['role'] === 'jefeBodega' || $user['role'] === 'JefeCedi') {
                    header("Location: ../JefeBodega/Bodega.php");
                    exit; // Asegúrate de salir después de redirigir
                } 
                elseif ($user['role'] === 'bodega') {
                    header("Location: ../Bodega/Bodega.php");
                    exit; // Asegúrate de salir después de redirigir
                }
                elseif ($user['role'] === 'despachos') {
                    header("Location: ../Despachos/Despachos.php");
                    exit; // Asegúrate de salir después de redirigir
                }
                elseif ($user['role'] === 'mensajeria') {
                    header("Location: ../Mensajeria/Mensajeria.php");
                    exit; // Asegúrate de salir después de redirigir
                }
                elseif ($user['role'] === 'Vendedor') {
                    header("Location: ../Vendedores/Vendedor.php");
                    exit; // Asegúrate de salir después de redirigir
                }
                else {
                    header("Location: user_dashboard.php");
                    exit; // Asegúrate de salir después de redirigir
                }
            }
        } else {
            echo "Credenciales incorrectas.";
        }
    } else {
      
    }
}
?>