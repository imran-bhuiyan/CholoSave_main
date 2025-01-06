<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; // Include database connection

if (!isset($_SESSION['group_id']) || !isset($_SESSION['user_id'])) {
    echo "Error: Group or User not set in session.";
    exit;
}

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_id = $_POST['poll_id'];
    $vote_option = $_POST['vote_option'];

    // Check if user has already voted
    $check_vote_query = "SELECT vote_id FROM polls_vote WHERE poll_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_vote_query);
    $stmt->bind_param("ii", $poll_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing vote
        $update_vote_query = "UPDATE polls_vote SET vote_option = ? WHERE poll_id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_vote_query);
        $stmt->bind_param("sii", $vote_option, $poll_id, $user_id);
    } else {
        // Insert new vote
        $insert_vote_query = "INSERT INTO polls_vote (poll_id, user_id, vote_option) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_vote_query);
        $stmt->bind_param("iis", $poll_id, $user_id, $vote_option);
    }
    $stmt->execute();
    exit;
}

// Fetch active polls and their vote counts
$polls_query = "SELECT p.poll_id, p.poll_question, 
    SUM(CASE WHEN pv.vote_option = 'Yes' THEN 1 ELSE 0 END) AS yes_votes,
    SUM(CASE WHEN pv.vote_option = 'No' THEN 1 ELSE 0 END) AS no_votes
    FROM polls p
    LEFT JOIN polls_vote pv ON p.poll_id = pv.poll_id
    WHERE p.group_id = ? AND p.status = 'active'
    GROUP BY p.poll_id";
$stmt = $conn->prepare($polls_query);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$polls_result = $stmt->get_result();

$polls = [];
while ($row = $polls_result->fetch_assoc()) {
    $polls[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polls</title>
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6">Polls</h1>
        <div id="polls-container" class="space-y-6">
            <?php foreach ($polls as $poll): ?>
                <?php
                $total_votes = $poll['yes_votes'] + $poll['no_votes'];
                $yes_percentage = $total_votes > 0 ? ($poll['yes_votes'] / $total_votes) * 100 : 0;
                $no_percentage = $total_votes > 0 ? ($poll['no_votes'] / $total_votes) * 100 : 0;
                ?>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-semibold mb-4"><?php echo htmlspecialchars($poll['poll_question']); ?></h3>
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="vote_<?php echo $poll['poll_id']; ?>" value="Yes" class="vote-option" data-poll-id="<?php echo $poll['poll_id']; ?>">
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="vote_<?php echo $poll['poll_id']; ?>" value="No" class="vote-option" data-poll-id="<?php echo $poll['poll_id']; ?>">
                            <span>No</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm">
                            <span>Yes: <?php echo round($yes_percentage); ?>%</span>
                            <span>No: <?php echo round($no_percentage); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-500 h-4 rounded-full" style="width: <?php echo $yes_percentage; ?>%"></div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 mt-2">
                            <div class="bg-red-500 h-4 rounded-full" style="width: <?php echo $no_percentage; ?>%"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        $(document).on('change', '.vote-option', function () {
            const pollId = $(this).data('poll-id');
            const voteOption = $(this).val();

            $.post('polls.php', { poll_id: pollId, vote_option: voteOption }, function () {
                // Reload the polls container to reflect updated data
                location.reload();
            });
        });
    </script>
</body>
</html>
