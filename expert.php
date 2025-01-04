<?php
require_once 'db.php';

// Handle form submission (image upload)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // Define upload directory within assets/expert_team
    $uploadDir = 'assets/expert_team/';
    
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Get file information
    $fileName = $_FILES['image']['name'];
    $fileTmpName = $_FILES['image']['tmp_name'];
    $fileError = $_FILES['image']['error'];
    $fileSize = $_FILES['image']['size'];

    // Generate a unique name for the file to avoid overwriting
    $fileNewName = uniqid('', true) . '-' . basename($fileName);
    $fileDestination = $uploadDir . $fileNewName;

    // Validate the file (e.g., ensure it's an image and not too large)
    if ($fileError === 0 && $fileSize < 5000000) { // Max size 5MB
        // Move the file to the upload directory
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            // Insert image path into database (or update for existing user)
            $sql = "INSERT INTO expert_team (name, email, phone, expertise, bio, image) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $email, $phone, $expertise, $bio, $fileDestination);
            $stmt->execute();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file.";
    }
}

// Fetch experts from database
$sql = "SELECT id, name, email, phone, expertise, bio, image FROM expert_team ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$experts = [];
while ($row = $result->fetch_assoc()) {
    $experts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Expert Team</title>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php foreach ($experts as $expert): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                    <div class="h-72 overflow-hidden">
                        <?php if ($expert['image']): ?>
                            <img src="<?php echo htmlspecialchars($expert['image']); ?>"
                                 alt="<?php echo htmlspecialchars($expert['name']); ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <!-- Fallback image if no image is available -->
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($expert['name']); ?>
                        </h3>
                        <p class="text-lg text-gray-600 mb-2">
                            <?php echo htmlspecialchars($expert['expertise']); ?>
                        </p>
                        <p class="text-gray-500 mb-4">
                            <a href="mailto:<?php echo htmlspecialchars($expert['email']); ?>" 
                               class="hover:text-blue-600 transition-colors">
                                <?php echo htmlspecialchars($expert['email']); ?>
                            </a>
                        </p>
                        <p class="text-gray-500 mb-4">
                            <strong>Contact: </strong> <?php echo htmlspecialchars($expert['phone']); ?>
                        </p>
                        <p class="text-gray-700 line-clamp-3">
                            <?php echo htmlspecialchars($expert['bio']); ?>
                        </p>
                        <button onclick="showFullBio(<?php echo $expert['id']; ?>)" 
                                class="mt-4 text-blue-600 hover:text-blue-800 transition-colors">
                            Read More
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Modal for full bio -->
        <div id="bioModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-8 rounded-xl max-w-2xl mx-4 relative">
                <button onclick="closeModal()" 
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h3 id="modalName" class="text-2xl font-bold mb-4"></h3>
                <p id="modalBio" class="text-gray-700"></p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

   
</body>
</html>
