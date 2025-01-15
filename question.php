<?php
session_start();
include 'db.php';
include 'includes/header2.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: forum.php');
    exit();
}

$question_id = mysqli_real_escape_string($conn, $_GET['id']);

// Update view count
$update_views = "UPDATE questions SET views = views + 1 WHERE id = '$question_id'";
mysqli_query($conn, $update_views);

// Fetch question with user info and stats
$question_query = "
    SELECT 
        q.*,
        u.name as author_name,
        COUNT(DISTINCT CASE WHEN rc.reaction_type = 'upvote' THEN rc.id END) as upvotes,
        COUNT(DISTINCT CASE WHEN rc.reaction_type = 'downvote' THEN rc.id END) as downvotes
    FROM questions q
    LEFT JOIN users u ON q.user_id = u.id
    LEFT JOIN reactions rc ON q.id = rc.question_id
    WHERE q.id = '$question_id'
    GROUP BY q.id
";
$question_result = mysqli_query($conn, $question_query);
$question = mysqli_fetch_assoc($question_result);

// Fetch replies with user info and reactions
$replies_query = "
    SELECT 
        r.*,
        u.name as author_name,
        COUNT(DISTINCT CASE WHEN rc.reaction_type = 'upvote' THEN rc.id END) as upvotes,
        COUNT(DISTINCT CASE WHEN rc.reaction_type = 'downvote' THEN rc.id END) as downvotes
    FROM replies r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN reactions rc ON r.id = rc.reply_id
    WHERE r.question_id = '$question_id'
    GROUP BY r.id
    ORDER BY r.created_at ASC
";
$replies_result = mysqli_query($conn, $replies_query);
$replies = [];
while ($reply = mysqli_fetch_assoc($replies_result)) {
    $replies[] = $reply;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($question['title']); ?> - Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Question Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">
                        <?php echo htmlspecialchars($question['title']); ?>
                    </h1>
                    <div class="prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($question['content'])); ?>
                    </div>
                    <div class="flex items-center mt-6 space-x-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($question['author_name']); ?>
                        </span>

                        <span class="text-sm text-gray-500">
                            <i class="fas fa-clock"></i>
                            <?php echo date('M d, Y', strtotime($question['created_at'])); ?>
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-eye"></i> <?php echo $question['views']; ?> views
                        </span>

                        <?php if ($_SESSION['user_id'] == $question['user_id']): ?>
                            <button onclick="deleteQuestion(<?php echo $question['id']; ?>)"
                                class="ml-4 text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i> Delete Question
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex flex-col items-center ml-4">
                    <button onclick="vote('question', <?php echo $question['id']; ?>, 'upvote')"
                        class="text-gray-500 hover:text-blue-500">
                        <i class="fas fa-arrow-up fa-lg"></i>
                    </button>
                    <span class="text-xl font-semibold my-2">
                        <?php echo $question['upvotes'] - $question['downvotes']; ?>
                    </span>
                    <button onclick="vote('question', <?php echo $question['id']; ?>, 'downvote')"
                        class="text-gray-500 hover:text-red-500">
                        <i class="fas fa-arrow-down fa-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo count($replies); ?> Replies</h2>

            <?php foreach ($replies as $reply): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between">
                        <div class="flex-1">
                            <div class="prose max-w-none">
                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                            </div>
                            <div class="flex items-center mt-4 space-x-4">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($reply['author_name']); ?>
                                </span>
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M d, Y', strtotime($reply['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col items-center ml-4">
                            <button onclick="vote('reply', <?php echo $reply['id']; ?>, 'upvote')"
                                class="text-gray-500 hover:text-blue-500">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <span class="text-lg font-semibold my-1">
                                <?php echo $reply['upvotes'] - $reply['downvotes']; ?>
                            </span>
                            <button onclick="vote('reply', <?php echo $reply['id']; ?>, 'downvote')"
                                class="text-gray-500 hover:text-red-500">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Reply Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add Your Reply</h3>
                <form action="submit_reply.php" method="POST">
                    <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                    <div class="mb-4">
                        <textarea name="content" rows="4" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            placeholder="Write your reply here..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition duration-200">
                            Post Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

        function deleteQuestion(questionId) {
            if (confirm('Are you sure you want to delete this question? This cannot be undone.')) {
                fetch('delete_question.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `question_id=${questionId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'forum.php';
                        } else {
                            alert('Error deleting question. Please try again.');
                        }
                    });
            }
        }
        function vote(type, id, voteType) {
            fetch('vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `type=${type}&id=${id}&vote_type=${voteType}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    </script>


</body>

</html>