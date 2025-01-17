<?php
include 'db.php';
session_start();

// Fetch all experts from database
$query = "SELECT * FROM expert_team ORDER BY name ASC";
$result = $conn->query($query);
$experts = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $experts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Experts - CholoSave</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .expert-card:hover .expert-overlay {
            opacity: 1;
        }
        .expert-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .contact-info {
            transition: all 0.3s ease;
        }
        .contact-info:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12">


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($experts as $expert): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden ">
                    <!-- Expert Image -->
                    <div class="relative">
                        <?php if ($expert['image']): ?>
                            <img src="uploads/experts/<?php echo htmlspecialchars($expert['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($expert['name']); ?>"
                                 class="expert-image">
                        <?php else: ?>
                            <div class="expert-image bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user-tie text-5xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Bio Overlay -->
                        <div class="expert-overlay absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 transition-all duration-300 flex items-end">
                            <div class="p-6 text-white">
                                <p class="text-sm leading-relaxed"><?php echo htmlspecialchars($expert['bio']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Expert Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($expert['name']); ?>
                        </h3>
                        <p class="text-blue-600 font-medium mb-4">
                            <?php echo htmlspecialchars($expert['expertise']); ?>
                        </p>
                        
                        <!-- Contact Information -->
                        <div class="space-y-2">
                            <div class="contact-info flex items-center text-gray-600">
                                <i class="fas fa-envelope w-6"></i>
                                <a href="mailto:<?php echo htmlspecialchars($expert['email']); ?>" 
                                   class="ml-2 hover:text-blue-600 transition-colors">
                                    <?php echo htmlspecialchars($expert['email']); ?>
                                </a>
                            </div>
                            <?php if ($expert['phone']): ?>
                                <div class="contact-info flex items-center text-gray-600">
                                    <i class="fas fa-phone w-6"></i>
                                    <a href="tel:<?php echo htmlspecialchars($expert['phone']); ?>" 
                                       class="ml-2 hover:text-blue-600 transition-colors">
                                        <?php echo htmlspecialchars($expert['phone']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($experts)): ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg">No experts found. Check back later!</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>