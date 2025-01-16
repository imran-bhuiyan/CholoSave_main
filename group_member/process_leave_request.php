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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_SESSION['group_id'];
    $user_id = $_SESSION['user_id'];
    
    // First check for approved loans
    $loan_query = "SELECT status FROM loan_request 
                  WHERE user_id = ? AND group_id = ? 
                  AND status = 'approved'";
    $loan_stmt = $conn->prepare($loan_query);
    $loan_stmt->bind_param("ii", $user_id, $group_id);
    $loan_stmt->execute();
    $loan_result = $loan_stmt->get_result();
    
    if ($loan_result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You cannot leave the group while you have outstanding loans. Please clear all loans before requesting to leave.'
        ]);
        $loan_stmt->close();
        exit;
    }
    $loan_stmt->close();
    
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
    
    // Update the leave_request status to pending
    $query = "UPDATE group_membership 
              SET leave_request = 'pending' 
              WHERE group_id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $group_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Leave request submitted successfully. Please wait for approval.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error submitting leave request. Please try again.'
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>