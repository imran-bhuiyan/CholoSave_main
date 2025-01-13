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
        <!-- Logo/Brand -->
        <div class="p-4 border-b">
            <div class="flex items-center space-x-2">
                <i class="fas fa-leaf text-green-500"></i>
                <span class="text-xl font-semibold">CholoSave</span>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <div class="space-y-2">
                <!-- Dashboard -->
                <a href="/test_project/group_admin/group_admin_dashboard.php"
                    class="sidebar-item flex items-center p-3 text-gray-700 rounded-lg">
                    <i class="fas fa-chart-line w-6"></i>
                    <span>Dashboard</span>
                </a>
                <!-- Members -->


                <!-- Financial Management Section -->
                <div class="pt-4">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Financial Management
                    </div>

                    <!-- Loans -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('loan-menu')">
                            <i class="fas fa-hand-holding-dollar w-6"></i>
                            <span>Loans</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="loan-menu" class="hidden ml-4 space-y-2 mt-1">
                            <a href="/test_project/group_admin/group_admin_loan_request.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request Loan</a>
                            <a href="/test_project/group_admin/group_admin_loan_history.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">My Loans</a>
                            <a href="/test_project/group_admin/group_members_loan_history.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Loans</a>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('payment-menu')">
                            <i class="fas fa-credit-card w-6"></i>
                            <span>Payments</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="payment-menu" class="hidden ml-4 space-y-2 mt-1">
                            <a href="/test_project/group_admin/payment-page.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Make Payment</a>
                            <a href="/test_project/group_admin/group_admin_payment_history.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">My Payment History</a>
                            <a href="/test_project/group_admin/group_members_payments.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Payments</a>
                        </div>
                    </div>

                    <!-- Investments -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('investments-menu')">
                            <i class="fas fa-piggy-bank w-6"></i>
                            <span>Investments</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="investments-menu" class="hidden ml-4 space-y-2 mt-1">
                            <a href="/test_project/group_admin/group_admin_investment.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">New Investment</a>
                            <a href="/test_project/group_admin/investment_returns.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Record Returns</a>
                            <a href="/test_project/group_admin/investment_history.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Investment History</a>
                        </div>
                    </div>
                </div>

                <!-- Group Management Section -->
                <div class="pt-4">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Group Management
                    </div>
                    <div class="mt-2">

                        <a href="/test_project/group_admin/group_members.php"
                            class="sidebar-item flex items-center p-3 text-gray-700 rounded-lg">
                            <i class="fas fa-users w-6"></i>
                            <span>Members</span>
                        </a>

                    </div>

                    <!-- Leave Requests -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('leave-menu')">
                            <i class="fas fa-calendar-day w-6"></i>
                            <span>Leave</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="leave-menu" class="hidden ml-4 space-y-2">
                            <a href="/test_project/group_admin/request_for_me.php" id="leaveRequestBtn"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request For Me</a>
                            <a href="/test_project/group_admin/member_leave_request.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Requests</a>
                        </div>
                    </div>

                    <!-- Withdraw -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('withdraw-menu')">
                            <i class="fas fa-wallet w-6"></i>
                            <span>Withdraw</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="withdraw-menu" class="hidden ml-4 space-y-2">
                            <a href="/test_project/group_admin/group_admin_withdraw_request.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Request For Me</a>
                            <a href="/test_project/group_admin/member_withdraw_request.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Member Requests</a>
                        </div>
                    </div>

                    <!-- Communication -->
                    <div class="mt-2">
                        <a href="#"
                            class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-comments w-6"></i>
                            <span>Chats</span>
                        </a>
                    </div>

                    <!-- Polls -->
                    <div class="mt-2">
                        <button
                            class="sidebar-item flex items-center w-full p-3 text-gray-600 hover:bg-gray-100 rounded-lg"
                            onclick="toggleSubMenu('polls-menu')">
                            <i class="fas fa-poll w-6"></i>
                            <span>Polls</span>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </button>
                        <div id="polls-menu" class="hidden ml-4 space-y-2">
                            <a href="/test_project/group_admin/admin_create_poll.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Create Poll</a>
                            <a href="/test_project/group_admin/admin_poll_history.php"
                                class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">View Polls</a>
                        </div>
                    </div>

                    <!-- Join Requests -->
                    <a href="/test_project/group_admin/member_join_request.php"
                        class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-user-plus w-6"></i>
                        <span>Join Request</span>
                    </a>
                </div>

                <!-- Settings Section -->
                <div class="pt-4">
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

                <!-- Exit -->
                <a href="/test_project/group_exit.php"
                    class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Exit</span>
                </a>
            </div>
        </nav>

        <!-- Theme Toggle -->
        <div class="p-4 border-t mt-auto">
            <button id="theme-toggle"
                class="sidebar-item flex items-center justify-center w-full p-2 rounded-lg hover:bg-gray-100">
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

        // Add the event listener for the leave request button
        document.getElementById('leaveRequestBtn').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default anchor behavior

            Swal.fire({
                title: 'Leave Group',
                text: 'Are you sure you want to leave this group?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, leave group',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send leave request to server
                    fetch('process_admin_leave_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Request Submitted',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error processing your request. Please try again.',
                                showConfirmButton: true
                            });
                        });
                }
            });
        });


    </script>
</body>

</html>