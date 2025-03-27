<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Web\WebDriver;

// Driver Configuration
$config = [
    'web' => [
        'matchingData' => [
            'driver' => 'web',
        ],
    ],
];

// BotMan Instance
$botman = BotManFactory::create($config);

// Database Connection
try {
    $db = new PDO("mysql:host=localhost;dbname=automuelles_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Database connected"); // Log instead of echo
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

// Message Handling
$botman->hears('enviar {user_name} {message}', function (BotMan $bot, $user_name, $message) use ($db) {
    error_log("Heard: enviar $user_name $message"); // Log entry

    // Look up receiver's user_id
    $stmt = $db->prepare("SELECT id FROM users WHERE name = :user_name");
    $stmt->execute(['user_name' => $user_name]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receiver) {
        $bot->reply("El usuario " . $user_name . " no existe.");
        error_log("User $user_name not found");
        return;
    }

    $receiver_id = $receiver['id'];
    error_log("Receiver ID: $receiver_id");

    // Check if receiver is active
    $activeStmt = $db->prepare("SELECT user_name FROM active_sessions WHERE user_name = :user_name");
    $activeStmt->execute(['user_name' => $user_name]);
    $isActive = $activeStmt->fetch(PDO::FETCH_ASSOC);

    if ($isActive) {
        $sender_id = 1;
        error_log("Attempting to insert: sender_id=$sender_id, receiver_id=$receiver_id, message=$message");

        // Insert message
        $insert = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (:sender_id, :receiver_id, :message, NOW())");
        $success = $insert->execute([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message
        ]);

        if ($success) {
            $bot->reply("Mensaje enviado a " . $user_name . ": " . $message);
            error_log("Message inserted successfully");
        } else {
            $error = implode(", ", $insert->errorInfo());
            $bot->reply("Error al guardar el mensaje: " . $error);
            error_log("Insert failed: " . $error);
        }
    } else {
        $bot->reply("El usuario " . $user_name . " no está activo.");
        error_log("User $user_name is not active");
    }
});

// Fallback
$botman->hears('(.*)', function (BotMan $bot, $message) {
    $bot->reply("Has enviado: " . $message);
    error_log("Fallback triggered: $message");
});

// Start listening
$botman->listen();