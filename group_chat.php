<?php
session_start();
//  include "group_member/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
$user_id = $_SESSION['user_id'];

// Check if the username is already set in the session
if (!isset($_SESSION['username'])) {
    // Fetch the user's name
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['name']; // Store username in the session
    } else {
        // User not found, destroy session and redirect to login
        session_destroy();
        header("Location: login.php");
        exit();
    }
    $stmt->close();
}

$group_id = $_SESSION['group_id'];
echo $user_id;
echo $group_id;

// Fetch group messages
$sql = "SELECT messages.*, users.name 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        WHERE group_id = ? 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">
    <div class="flex h-screen">
        <?php include 'group_member/sidebar.php'; ?> <!-- Sidebar inclusion -->

        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <a href="group_member/group_member_dashboard.php" 
                           class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-semibold text-gray-800">Group Chat</h1>
                    </div>
                    <a href="logout.php" 
                       class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">
                        Logout
                    </a>
                </div>
            </header>
            
            <!-- Chat Container -->
            <div class="flex-1 max-w-7xl mx-auto w-full p-4 overflow-hidden">
                <div class="bg-white rounded-lg shadow-lg h-full flex flex-col">
                    <!-- Messages Area -->
                    <div class="flex-1 p-4 overflow-y-auto messages">
                        <?php foreach ($messages as $message): ?>
                            <div class="message chat mb-4 <?php echo $message['user_id'] === $user_id ? 'flex justify-end' : 'flex justify-start'; ?>">
                                <div class="max-w-[70%]">
                                    <div class="flex items-center mb-1 <?php echo $message['user_id'] === $user_id ? 'justify-end' : 'justify-start'; ?>">
                                        <span class="text-sm text-gray-600 font-medium">
                                            <?php echo htmlspecialchars($message['name']); ?>
                                        </span>
                                        <span class="text-xs text-gray-400 ml-2">
                                            <?php echo date('h:i A', strtotime($message['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="<?php echo $message['user_id'] === $user_id 
                                        ? 'bg-blue-500 text-white rounded-tl-lg rounded-tr-lg rounded-bl-lg' 
                                        : 'bg-gray-100 text-gray-800 rounded-tl-lg rounded-tr-lg rounded-br-lg'; ?> 
                                        p-3 shadow-sm">
                                        <?php echo htmlspecialchars($message['message']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Message Input Area -->
                    <div class="border-t p-4 bg-gray-50">
                        <form class="message-form flex gap-2">
                            <div class="flex-1">
                                <textarea 
                                    name="message" 
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                    rows="2"
                                    placeholder="Type your message..."
                                ></textarea>
                            </div>
                            <button 
                                type="submit" 
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 flex items-center"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const groupId = <?php echo json_encode($group_id); ?>;
        const userId = <?php echo json_encode($user_id); ?>;
        const username = <?php echo json_encode($_SESSION['username']); ?>;

        const socket = new WebSocket('ws://localhost:8080/chat');

        socket.onopen = function () {
            console.log('WebSocket connection established.');
        };

        socket.onmessage = function (event) {
            const data = JSON.parse(event.data);

            if (data.group_id === groupId) {
                const messagesDiv = document.querySelector('.messages');
                const messageElem = document.createElement('div');
                const time = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
                
                messageElem.className = `message chat mb-4 ${data.user_id === userId ? 'flex justify-end' : 'flex justify-start'}`;
                messageElem.innerHTML = `
                    <div class="max-w-[70%]">
                        <div class="flex items-center mb-1 ${data.user_id === userId ? 'justify-end' : 'justify-start'}">
                            <span class="text-sm text-gray-600 font-medium">${data.username}</span>
                            <span class="text-xs text-gray-400 ml-2">${time}</span>
                        </div>
                        <div class="${data.user_id === userId 
                            ? 'bg-blue-500 text-white rounded-tl-lg rounded-tr-lg rounded-bl-lg' 
                            : 'bg-gray-100 text-gray-800 rounded-tl-lg rounded-tr-lg rounded-br-lg'} 
                            p-3 shadow-sm">
                            ${data.message}
                        </div>
                    </div>`;
                messagesDiv.appendChild(messageElem);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        };

        function sendMessage() {
            const messageInput = document.querySelector('textarea[name="message"]');
            const message = messageInput.value.trim();

            if (message !== '') {
                const msgData = {
                    group_id: groupId,
                    user_id: userId,
                    username: username,
                    message: message,
                };

                socket.send(JSON.stringify(msgData));
                messageInput.value = '';
            }
        }

        document.querySelector('.message-form').addEventListener('submit', function (e) {
            e.preventDefault();
            sendMessage();
        });

        // Auto-scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            const messagesDiv = document.querySelector('.messages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });
    </script>
</body>

</html>
