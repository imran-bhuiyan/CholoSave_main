<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all contact messages with their replies
$query = "SELECT c.*, 
          GROUP_CONCAT(r.body SEPARATOR '|||') as replies,
          GROUP_CONCAT(r.created_at SEPARATOR '|||') as reply_dates,
          GROUP_CONCAT(u.name SEPARATOR '|||') as reply_names
          FROM contact_us c
          LEFT JOIN replies r ON c.id = r.question_id
          LEFT JOIN users u ON r.user_id = u.id
          GROUP BY c.id
          ORDER BY c.created_at DESC";
$result = $conn->query($query);
$messages = $result->fetch_all(MYSQLI_ASSOC);

// Handle message deletion
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    
    // First delete associated replies
    $delete_replies = "DELETE FROM replies WHERE question_id = ?";
    $stmt = $conn->prepare($delete_replies);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    
    // Then delete the message
    $delete_query = "DELETE FROM contact_us WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $message_id);
    
    if ($stmt->execute()) {
        header("Location: contact_us.php?deleted=1");
        exit();
    }
}

// Handle saving reply
if (isset($_POST['action']) && $_POST['action'] === 'reply') {
    $message_id = $_POST['message_id'];
    $reply_text = $_POST['reply_message'];
    $admin_id = $_SESSION['user_id'];
    
    $reply_query = "INSERT INTO replies (question_id, user_id, body) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($reply_query);
    $stmt->bind_param("iis", $message_id, $admin_id, $reply_text);
    
    if ($stmt->execute()) {
        header("Location: contact_us.php?reply_sent=1");
        exit();
    } else {
        header("Location: contact_us.php?reply_error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - CholoSave</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 custom-font dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between p-4 bg-white shadow dark:bg-gray-800">
                <h1 class="text-2xl font-semibold dark:text-white">
                    <i class="fas fa-envelope mr-2"></i>Contact Messages
                </h1>
                <button id="darkModeToggle" class="p-2 hover:bg-gray-100 rounded-full dark:hover:bg-gray-700">
                    <i class="fas fa-moon dark:text-white"></i>
                </button>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        Message deleted successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['reply_sent'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        Reply sent successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['reply_error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        Error sending reply. Please try again.
                    </div>
                <?php endif; ?>

                <!-- Messages Grid -->
                <div class="grid gap-6">
                    <?php foreach ($messages as $message): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold dark:text-white">
                                            <?php echo htmlspecialchars($message['name']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-envelope mr-2"></i>
                                            <?php echo htmlspecialchars($message['email']); ?>
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-clock mr-2"></i>
                                            <?php echo date('F j, Y g:i A', strtotime($message['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="openReplyModal(<?php echo $message['id']; ?>)"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-full dark:hover:bg-blue-900">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button onclick="deleteMessage(<?php echo $message['id']; ?>)"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-full dark:hover:bg-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="prose dark:prose-invert max-w-none">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        <?php echo nl2br(htmlspecialchars($message['description'])); ?>
                                    </p>
                                </div>

                                <!-- Display Replies -->
                                <?php if (!empty($message['replies'])): 
                                    $replies = explode('|||', $message['replies']);
                                    $reply_dates = explode('|||', $message['reply_dates']);
                                    $reply_names = explode('|||', $message['reply_names']);
                                ?>
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <h4 class="text-lg font-semibold mb-3 dark:text-white">Replies</h4>
                                        <?php for($i = 0; $i < count($replies); $i++): ?>
                                            <div class="mb-3 pl-4 border-l-2 border-indigo-500">
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                                    <span class="font-medium"><?php echo htmlspecialchars($reply_names[$i]); ?></span>
                                                    <span class="mx-2">•</span>
                                                    <span><?php echo date('F j, Y g:i A', strtotime($reply_dates[$i])); ?></span>
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300">
                                                    <?php echo nl2br(htmlspecialchars($replies[$i])); ?>
                                                </p>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Reply Modal -->
    <div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Reply to Message</h2>
                <button onclick="closeReplyModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="replyForm" method="POST" action="contact_us.php">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="message_id" id="reply_message_id">
                
                <div class="mb-4">
                    <label for="reply_message" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Reply
                    </label>
                    <textarea id="reply_message" name="reply_message" rows="6" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReplyModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReplyModal(messageId) {
            document.getElementById('reply_message_id').value = messageId;
            document.getElementById('replyModal').classList.remove('hidden');
            document.getElementById('replyModal').classList.add('flex');
        }
        
        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
            document.getElementById('replyModal').classList.remove('flex');
            document.getElementById('replyForm').reset();
        }
        
        function deleteMessage(messageId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'contact_us.php';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    
                    const messageInput = document.createElement('input');
                    messageInput.type = 'hidden';
                    messageInput.name = 'message_id';
                    messageInput.value = messageId;
                    
                    form.appendChild(actionInput);
                    form.appendChild(messageInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const icon = darkModeToggle.querySelector('i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
        });

        // Close modal when clicking outside
        document.getElementById('replyModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('replyModal')) {
                closeReplyModal();
            }
        });

        // Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 3000);
            });
        });
    </script>
</body>
</html>