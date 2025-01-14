<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Design</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4">
        <!-- Header -->
        <header class="bg-white shadow p-4 rounded-lg mb-6">
            <h1 class="text-2xl font-bold text-blue-600">Forum</h1>
        </header>

        <div class="grid grid-cols-12 gap-4">
            <!-- Question Feed -->
            <div class="col-span-3 bg-white p-4 shadow rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Questions</h2>
                <ul class="space-y-2">
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">How to start with React?</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">Best Python libraries for data science?</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">What is Tailwind CSS?</li>
                </ul>
            </div>

            <!-- Main Thread -->
            <div class="col-span-6 bg-white p-4 shadow rounded-lg">
                <h2 class="text-xl font-bold mb-4">How to start with React?</h2>
                <p class="text-gray-700 mb-6">React is a JavaScript library for building user interfaces. To get started, you can use Create React App or dive into Vite for a faster setup.</p>

                <h3 class="text-lg font-semibold mb-2">Replies</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-gray-100 rounded">
                        <p class="text-gray-700">Start with the official React documentation. It's comprehensive and easy to follow.</p>
                        <p class="text-sm text-gray-500 mt-2">- User123</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded">
                        <p class="text-gray-700">You can also check out tutorials on YouTube for quick hands-on experience.</p>
                        <p class="text-sm text-gray-500 mt-2">- DevGuru</p>
                    </div>
                </div>

                <!-- Reply Input -->
                <div class="mt-6">
                    <textarea class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Write your reply..."></textarea>
                    <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Post Reply</button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-3 bg-white p-4 shadow rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Categories</h2>
                <ul class="space-y-2">
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">JavaScript</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">Python</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">CSS</li>
                </ul>

                <h2 class="text-lg font-semibold mt-6 mb-4">Trending Topics</h2>
                <ul class="space-y-2">
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">Next.js vs. React</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">Best IDEs for Web Development</li>
                    <li class="p-2 bg-gray-100 rounded hover:bg-gray-200 cursor-pointer">AI in Web Development</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
