<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Consultar usuario por email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
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
                } elseif ($user['role'] === 'bodega' || $user['role'] === 'jefeBodega') {
                    header("Location: ../Bodega/Bodega.php");
                    exit; // Asegúrate de salir después de redirigir
                } elseif ($user['role'] === 'despachos') {
                    header("Location: ../Despachos/Despachos.php");
                    exit; // Asegúrate de salir después de redirigir
                }
                elseif ($user['role'] === 'mensajeria') {
                    header("Location: ../Mensajeria/Mensajeria.php");
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