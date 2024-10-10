<?php
session_start();
include('../config/db.php');

if (!(isset($_SESSION['adminID']))) {
    // header("Location: login.php");
    echo "<script>window.location.href='login.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testId = $_POST['test_id'];

    $stmt = $conn->prepare("
        SELECT 
        u.username,
        (SELECT COUNT(*) FROM questions WHERE test_id = ?) AS total_questions,
        COUNT(ur.is_correct) AS total_responses,
        IFNULL(SUM(ur.is_correct), 0) AS correct_answers
        FROM 
        user u
        JOIN 
        user_responses ur ON u.id = ur.user_id
        WHERE 
        ur.test_id = ? AND
        JSON_CONTAINS(u.completed_tests,?,'$')
        GROUP BY 
        u.username
        ORDER BY 
        u.username
        ");
    $testIdString = json_encode((string)$testId);
    $stmt->bind_param('iis', $testId,$testId,$testIdString);  
    $stmt->execute();
    $result = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Generate Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <?php include('./includes/sidebar.php')?>

        <!-- Main Content -->
        <div class="content flex-1 bg-[#ecf0f5] p-4 lg:p-8">
            <div class="w-full">
                <h1 class="text-2xl font-semibold mb-6">Report Generation</h1>
                <div class="w-full lg:w-1/2 bg-white p-6 rounded-md shadow-lg">
                    <form class="space-y-6" action="" method="post">
                        <h2 class="text-xl font-medium mb-4 text-center">Generate Test Report</h2>
                        <div class="space-y-4">
                            <label class="block text-gray-700">Select test</label>
                            <select name="test_id" class="border w-full p-2 rounded-lg" required>
                                <option value="">--------SELECT TEST-------</option>
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM tests");
                                $stmt->execute();
                                $res = $stmt->get_result();

                                while($row = $res->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['title']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-green-500 px-4 py-2 text-white rounded hover:bg-green-600 transition duration-150">Generate Report</button>
                        </div>
                    </form>
                </div>

                <!-- Display report if result exists -->
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $result->num_rows > 0): ?>
                    <div class="mt-8">
                        <h2 class="text-xl font-medium mb-4">Report for Selected Test</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border rounded-lg overflow-hidden shadow">
                                <thead class="bg-gray-200 text-gray-600">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Username</th>
                                        <th class="px-4 py-2 text-left">Total Questions</th>
                                        <th class="px-4 py-2 text-left">Total Responses</th>
                                        <th class="px-4 py-2 text-left">Correct Answers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['total_questions']); ?></td>
                                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['total_responses']); ?></td>
                                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['correct_answers']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                    <p class="text-red-500 mt-4">No data available for the selected test.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
