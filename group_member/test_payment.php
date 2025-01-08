<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSLCommerz Payment Gateway</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Payment Form Page -->
    <div id="payment-form" class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-white text-xl font-bold">Secure Payment</h1>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Amount to Pay:</span>
                        <span class="text-xl font-bold text-blue-600">৳1,299.00</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full">
                        <div class="h-2 bg-blue-600 rounded-full w-full"></div>
                    </div>
                </div>

                <form>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                            <input type="text" placeholder="0000 0000 0000 0000" class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="text" placeholder="MM/YY" class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                <input type="text" placeholder="123" class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Holder Name</label>
                            <input type="text" placeholder="Full Name" class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <button type="button" onclick="showSuccess()" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Pay Now
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="flex items-center justify-center space-x-4">
                        <img src="/api/placeholder/50/30" alt="Visa" class="h-8">
                        <img src="/api/placeholder/50/30" alt="Mastercard" class="h-8">
                        <img src="/api/placeholder/50/30" alt="American Express" class="h-8">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Page -->
    <div id="success-page" class="hidden container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful!</h2>
                <p class="text-gray-600 mb-6">Your transaction has been completed successfully.</p>
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-bold">৳1,299.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="font-mono">TXN123456789</span>
                    </div>
                </div>
                <button onclick="window.print()" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Download Receipt
                </button>
            </div>
        </div>
    </div>

    <script>
        function showSuccess() {
            document.getElementById('payment-form').classList.add('hidden');
            document.getElementById('success-page').classList.remove('hidden');
        }
    </script>
</body>
</html>