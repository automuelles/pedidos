document.addEventListener("DOMContentLoaded", function () {
    const chatButton = document.getElementById("chat-button");
    const chatContainer = document.getElementById("chat-container");
    const closeChat = document.getElementById("close-chat");
    const sendButton = document.getElementById("send-btn");
    const chatMessages = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chat-input");
    const usersList = document.getElementById("users-list");

    let selectedUserId = null;

    chatButton.addEventListener("click", () => {
        chatContainer.style.display = "flex";
    });

    closeChat.addEventListener("click", () => {
        chatContainer.style.display = "none";
    });

    function loadUsers() {
        fetch("../Chat/chat.php?action=get_users")
            .then(response => response.json())
            .then(data => {
                usersList.innerHTML = "<h4>Usuarios Conectados:</h4>";
                data.forEach(user => {
                    usersList.innerHTML += `
                        <p class="user-item" data-user-id="${user.user_id}">
                            ${user.user_name}
                        </p>`;
                });

                document.querySelectorAll('.user-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectedUserId = this.dataset.userId;
                        document.querySelectorAll('.user-item').forEach(el => el.style.backgroundColor = '');
                        this.style.backgroundColor = '#e0e0e0';
                        loadMessages();
                    });
                });
            })
            .catch(error => console.error('Error loading users:', error));
    }

    function loadMessages() {
        const url = selectedUserId 
            ? `../Chat/chat.php?action=get_messages&recipient_id=${selectedUserId}`
            : "../Chat/chat.php?action=get_messages";
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = "";
                data.forEach(msg => {
                    chatMessages.innerHTML += `
                        <p><strong>${msg.name}:</strong> ${msg.message}
                        <br><small>${msg.timestamp}</small></p>`;
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    sendButton.addEventListener("click", () => {
        const message = chatInput.value.trim();
        if (message !== "") {
            const url = selectedUserId 
                ? `../Chat/chat.php?recipient_id=${selectedUserId}`
                : "../Chat/chat.php";

            fetch(url, {
                method: "POST",
                body: new URLSearchParams({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    chatInput.value = "";
                    loadMessages();
                }
            })
            .catch(error => console.error('Error sending message:', error));
        }
    });

    setInterval(() => {
        loadMessages();
        loadUsers();
        fetch("../Chat/chat.php?action=keep_alive");
    }, 3000);

    loadUsers();
    loadMessages();
});
