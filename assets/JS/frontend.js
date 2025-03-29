// frontend.js (Chat flotante con PHP y AJAX)

let chatOpen = false;

// Crear botón flotante
const chatButton = document.createElement("div");
chatButton.innerHTML = "💬";
chatButton.style = "position: fixed; bottom: 20px; right: 20px; background: #28a745; color: white; padding: 15px; border-radius: 50%; cursor: pointer; font-size: 24px; text-align: center; z-index: 9999;";
document.body.appendChild(chatButton);

// Crear ventana de chat
const chatBox = document.createElement("div");
chatBox.style = "display: none; position: fixed; bottom: 80px; right: 20px; width: 300px; height: 400px; background: #ffffff; border: 1px solid #ccc; box-shadow: 0px 0px 10px rgba(0,0,0,0.2); overflow: hidden; z-index: 9999;";
chatBox.innerHTML = `
  <div style="background: #343a40; color: #ffffff; padding: 10px; text-align: center;">Chat en tiempo real</div>
  <div id="chat-users" style="height: 50px; background: #e9ecef; color: #212529; padding: 5px; overflow-x: auto; white-space: nowrap;"></div>
  <div id="chat-messages" style="height: 250px; overflow-y: auto; padding: 10px; background: #f8f9fa; color: #212529;"></div>
  <input id="chat-input" type="text" placeholder="Escribe un mensaje..." style="width: 100%; padding: 10px; border: none; border-top: 1px solid #ccc; background: #ffffff; color: #212529;">
`;
document.body.appendChild(chatBox);

// Mostrar/Ocultar chat
chatButton.addEventListener("click", () => {
    chatOpen = !chatOpen;
    chatBox.style.display = chatOpen ? "block" : "none";
    if (chatOpen) {
        loadMessages();
        loadUsers(); // Load users when chat is opened
    }
});

// Enviar mensajes
const loggedInUserId = ""; // Replace with the actual ID of the logged-in user

const chatInput = document.getElementById("chat-input");

chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter" && chatInput.value.trim() !== "") {
        const selectedUserId = document.querySelector('input[name="user"]:checked').value; // Get selected user ID
        sendMessage(chatInput.value, loggedInUserId, selectedUserId); // Send message to selected user
        chatInput.value = "";
    }
});

const socket = io("http://localhost:3000"); // Conectar al servidor Socket.IO

// Función para enviar mensajes vía Socket.IO
function sendMessage(message, userId, recipientId) {
    socket.emit('sendMessage', { message, userId, recipientId }); // Emitir el mensaje al servidor
}

// Función para cargar mensajes
function loadMessages() {
    socket.emit('loadMessages', loggedInUserId); // Emitir evento para cargar mensajes

    socket.on('loadMessages', (messages) => {
        const messagesContainer = document.getElementById("chat-messages");
        messagesContainer.innerHTML = messages.map(msg => {
            return `<p><strong>${msg.sender}:</strong> <span class='message-text'>${msg.message}</span></p>`;
        }).join('');
    });
}

// Función para obtener la lista de usuarios en línea
function loadUsers() {
    fetch("../Chat/get_users.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("chat-users").innerHTML = data;
        });
}

// Actualizar mensajes cada 3 segundos
setInterval(loadMessages, 3000);
