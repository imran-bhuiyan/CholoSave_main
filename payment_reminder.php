

<?php
// Database connection and the query for notification
include 'db.php';

$query = "
INSERT INTO notifications (target_group_id, type, title, message, status)
SELECT 
    g.group_id,
    'payment_reminder',
    'Payment Reminder',
    CONCAT('Reminder: You have a payment due tomorrow. Please make sure to make the payment on time.'),
    'unread'
FROM 
    my_group g
WHERE 
    (g.dps_type = 'monthly' AND DATE_ADD(g.start_date, INTERVAL 1 MONTH) = CURDATE() + INTERVAL 1 DAY)
    OR
    (g.dps_type = 'weekly' AND DATE_ADD(g.start_date, INTERVAL 7 DAY) = CURDATE() + INTERVAL 1 DAY);
";

if ($conn->query($query) === TRUE) {
    echo "Payment reminders sent successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
