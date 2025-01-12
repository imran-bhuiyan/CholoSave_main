<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="sidebar" class="hidden md:flex flex-col w-64 bg-white shadow-lg dark-mode-transition">
        <div class="p-4 border-b">
            <div class="flex items-center space-x-2">
                <i class="fas fa-leaf text-green-500"></i>
                <span class="text-xl font-semibold">CholoSave</span>
            </div>
        </div>

        <nav class="flex-1 p-4">
            <div class="space-y-2">
                <a href="/test_project/group_admin/group_admin_dashboard.php"
                    class="sidebar-item flex items-center p-3 text-gray-700 rounded-lg">
                    <i class="fas fa-chart-line w-6"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Emergency Loan Request -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('emergency-loan-menu')">
                        <i class="fas fa-hand-holding-dollar w-6"></i>
                        <span>Loan</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="emergency-loan-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/group_admin_loan_request.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request For Me</a>
                        <a href="/test_project/group_admin/member_requests.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Requests</a>
                    </div>
                </div>

                <!-- Leave Request -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('leave-request-menu')">
                        <i class="fas fa-calendar-day w-6"></i>
                        <span>Leave</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="leave-request-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/request_for_me.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request For Me</a>
                        <a href="/test_project/group_admin/member_requests.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Requests</a>
                    </div>
                </div>

                <!-- Loan History -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('loan-history-menu')">
                        <i class="fas fa-history w-6"></i>
                        <span>Loan History</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="loan-history-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/my_loans.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">My Loans</a>
                        <a href="/test_project/group_admin/member_loans.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Loans</a>
                    </div>
                </div>

                <!-- Payment History -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('payment-history-menu')">
                        <i class="fas fa-credit-card w-6"></i>
                        <span>Payment</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="payment-history-menu" class="hidden ml-4 space-y-2">
                    <a href="/test_project/group_admin/my_payments.php"
                    class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Pay</a>
                        <a href="/test_project/group_admin/my_payments.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">My Payments</a>
                        <a href="/test_project/group_admin/member_payments.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Payments</a>
                    </div>
                </div>

                <!-- Withdraw Request -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('withdraw-request-menu')">
                        <i class="fas fa-wallet w-6"></i>
                        <span>Withdraw</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="withdraw-request-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/request_for_me.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request For Me</a>
                        <a href="/test_project/group_admin/member_requests.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Requests</a>
                    </div>
                </div>

                <a href="#" class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-comments w-6"></i>
                    <span>Chats</span>
                </a>

                <div>
                    <a href="#" class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('polls-menu')">
                        <i class="fas fa-poll w-6"></i>
                        <span>Polls</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </a>

                    <!-- Polls -->
                    <div id="polls-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/create_poll.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Create Poll</a>
                        <a href="/test_project/group_admin/view_polls.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">View Polls</a>
                    </div>
                </div>
                <!-- Investments -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('investments-menu')">
                        <i class="fas fa-piggy-bank w-6"></i>
                        <span>Investments</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="investments-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/data_input.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Data Input</a>
                        <a href="/test_project/group_admin/history.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">History</a>
                        <a href="/test_project/group_admin/return_input.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Return Input</a>
                    </div>
                </div>

                <a href="/test_project/group_admin/join_request.php"
                    class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-user-plus w-6"></i>
                    <span>Join Request</span>
                </a>

                <!-- Settings -->
                <div>
                    <button class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                        onclick="toggleSubMenu('settings-menu')">
                        <i class="fas fa-cogs w-6"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </button>
                    <div id="settings-menu" class="hidden ml-4 space-y-2">
                        <a href="/test_project/group_admin/change_goal.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Change Goal Amount</a>
                        <a href="/test_project/group_admin/change_installment.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Change Installment Amount</a>
                        <a href="/test_project/group_admin/change_time.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Change Time Period</a>
                        <a href="/test_project/group_admin/close_savings.php"
                            class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Close Savings</a>
                    </div>
                </div>

                <a href="/test_project/group_exit.php"
                    class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Exit</span>
                </a>
            </div>
        </nav>

        <!-- Theme Toggle -->
        <div class="p-4 border-t mt-36">
            <button id="theme-toggle" class="flex items-center justify-center w-full p-2 rounded-lg hover:bg-gray-100 ">
                <i class="fas fa-moon mr-2"></i>
                <span>Dark Mode</span>
            </button>
        </div>
    </div>


    <script>
        function toggleSubMenu(menuId) {
            const menu = document.getElementById(menuId);
            menu.classList.toggle('hidden');
        }
    </script>
</body>

</html>