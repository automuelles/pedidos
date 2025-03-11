<?php
session_start();
include '../php/db.php'; // Archivo de conexión a la base de datos con PDO

// Función para registrar sesión activa
function registerActiveSession($pdo, $user_id, $user_name) {
    $session_id = session_id();
    $pdo->exec("DELETE FROM active_sessions WHERE user_id = '$user_id'");
    $query = "INSERT INTO active_sessions (session_id, user_id, user_name) 
              VALUES (:session_id, :user_id, :user_name)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':session_id' => $session_id,
        ':user_id' => $user_id,
        ':user_name' => $user_name
    ]);
}

// Función para eliminar sesión activa
function removeActiveSession($pdo) {
    $session_id = session_id();
    $query = "DELETE FROM active_sessions WHERE session_id = :session_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':session_id' => $session_id]);
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "Acceso denegado"]));
}

// Obtener usuarios conectados
if (isset($_GET['action']) && $_GET['action'] == 'get_users') {
    $query = "SELECT user_id, user_name FROM active_sessions";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($users);
    exit;
}

// Obtener mensajes (filtrados por conversación si hay recipient_id)
if (isset($_GET['action']) && $_GET['action'] == 'get_messages') {
    $user_id = $_SESSION['user_id'];
    $recipient_id = isset($_GET['recipient_id']) ? $_GET['recipient_id'] : null;
    
    $query = "SELECT users.name, messages.message, messages.timestamp 
              FROM messages 
              JOIN users ON messages.user_id = users.id 
              WHERE ";
    
    if ($recipient_id) {
        $query .= "((messages.user_id = :user_id AND messages.recipient_id = :recipient_id) 
                   OR (messages.user_id = :recipient_id AND messages.recipient_id = :user_id))";
    } else {
        $query .= "messages.recipient_id IS NULL";
    }
    
    $query .= " ORDER BY messages.timestamp ASC LIMIT 50";
    
    $stmt = $pdo->prepare($query);
    if ($recipient_id) {
        $stmt->execute([
            ':user_id' => $user_id,
            ':recipient_id' => $recipient_id
        ]);
    } else {
        $stmt->execute([':user_id' => $user_id]);
    }
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}

// Enviar mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];
    $recipient_id = isset($_GET['recipient_id']) ? $_GET['recipient_id'] : null;
    
    $query = "INSERT INTO messages (user_id, message" . ($recipient_id ? ", recipient_id" : "") . ") 
              VALUES (:user_id, :message" . ($recipient_id ? ", :recipient_id" : "") . ")";
    $stmt = $pdo->prepare($query);
    
    $params = [
        ':user_id' => $user_id,
        ':message' => $message
    ];
    if ($recipient_id) {
        $params[':recipient_id'] = $recipient_id;
    }
    
    if ($stmt->execute($params)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $pdo->errorInfo()]);
    }
    exit;
}

// Mantener la sesión activa
if (isset($_GET['action']) && $_GET['action'] == 'keep_alive') {
    $session_id = session_id();
    $query = "UPDATE active_sessions SET login_time = CURRENT_TIMESTAMP WHERE session_id = :session_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':session_id' => $session_id]);
    
    $pdo->exec("DELETE FROM active_sessions WHERE login_time < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    echo json_encode(["status" => "success"]);
    exit;
}

// Manejar cierre de sesión
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    removeActiveSession($pdo);
    session_destroy();
    echo json_encode(["status" => "success"]);
    exit;
}
?>