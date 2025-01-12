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



<div id="sidebar" class="hidden md:flex flex-col w-64 bg-white shadow-lg dark-mode-transition">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-2">
            <i class="fas fa-leaf text-green-500"></i>
            <span class="text-xl font-semibold">CholoSave</span>
        </div>
    </div>

    <nav class="flex-1 p-4">
        <div class="space-y-2">
            <a href="/test_project/group_member/group_member_dashboard.php"
                class="sidebar-item flex items-center p-3 text-gray-700 rounded-lg">
                <i class="fas fa-chart-line w-6"></i>
                <span>Dashboard</span>
            </a>
            <a href="/test_project/group_member/group_member_emergency_loan_req.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-hand-holding-dollar w-6"></i>
                <span>Emergency Loan Request</span>
            </a>
            <a href="/test_project/chat/group.php" class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-comments w-6"></i>
                <span>Chats</span>
            </a>
            <a href="/test_project/group_member/group_member_list.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-users w-6"></i>
                <span>Members</span>
            </a>
            <a href="/test_project/group_member/payment-page.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-credit-card w-6"></i>
                <span>Payment</span>
            </a>
            <a href="#" id="leaveRequestBtn"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-calendar-day w-6"></i>
                <span>Leave Request</span>
            </a>
            <a href="/test_project/group_member/group_member_loan_history.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-history w-6"></i>
                <span>Loan History</span>
            </a>
            <a href="/test_project/group_member/group_member_payment_history.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-history w-6"></i>
                <span>Payment History</span>
            </a>
            <a href="/test_project/group_member/group_member_withdraw_request.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-wallet w-6"></i>
                <span>Withdraw Request</span>
            </a>
            <a href="/test_project/group_member/group_investment_details.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-piggy-bank w-6"></i>
                <span>Investment Details</span>
            </a>
            <a href="http://localhost/test_project/group_exit.php"
                class="sidebar-item flex items-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-sign-out-alt w-6"></i>
                <span>Exit</span>
            </a>
        </div>
    </nav>

    <!-- Theme Toggle -->
    <div class="p-4 border-t">
        <button id="theme-toggle" class="flex items-center justify-center w-full p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-moon mr-2"></i>
            <span>Dark Mode</span>
        </button>
    </div>
</div>


<script>
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
                fetch('process_leave_request.php', {
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