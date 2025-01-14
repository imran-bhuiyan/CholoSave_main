<?php
session_start();

// Check if group_id and user_id are set in session
if (!isset($_SESSION['group_id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired']);
    exit;
}

// Ensure database connection
if (!isset($conn)) {
    include 'db.php';
}

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

// Check if the user is an admin for the group
$is_admin = false;
$checkAdminQuery = "SELECT group_admin_id FROM my_group WHERE group_id = ?";
if ($stmt = $conn->prepare($checkAdminQuery)) {
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $stmt->bind_result($group_admin_id);
    $stmt->fetch();
    $stmt->close();
    
    // If the user is the admin of the group, proceed; otherwise, redirect to an error page
    if ($group_admin_id === $user_id) {
        $is_admin = true;
    }
}

if (!$is_admin) {
    // Redirect to error page if the user is not an admin
    header("Location: /test_project/error_page.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user already has a pending leave request
    $check_query = "SELECT leave_request FROM group_membership 
                   WHERE group_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $group_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $membership = $result->fetch_assoc();
    
    if ($membership && $membership['leave_request'] === 'pending') {
        echo json_encode([
            'status' => 'error',
            'message' => 'You already have a pending leave request'
        ]);
        exit;
    }

    // Check if there are more than one admin in the group
    $adminCountQuery = "SELECT COUNT(*) FROM group_membership WHERE group_id = ? AND is_admin = 1";
    $stmt = $conn->prepare($adminCountQuery);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($admin_count);
    $stmt->fetch();
    $stmt->close();

    // If there is more than one admin, proceed with the leave request
    if ($admin_count > 1) {
        // Update the leave request status to 'pending'
        $updateLeaveRequestQuery = "UPDATE group_membership SET leave_request = 'pending' WHERE group_id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($updateLeaveRequestQuery)) {
            $stmt->bind_param("ii", $group_id, $user_id);
            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Leave request has been submitted successfully'
                ]);
                exit;
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to submit leave request'
                ]);
                exit;
            }
        }
    } else {
        // If there is only one admin, show an error message
        echo json_encode([
            'status' => 'error',
            'message' => 'You cannot leave the group. Please assign another admin before leaving.'
        ]);
        exit;
    }
}
?>
