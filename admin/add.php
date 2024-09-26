<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    header("Location: login.php");
    exit();
}
?>
<?php
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare('INSERT INTO tests(title, start_time, end_time) VALUES(?, ?, ?)');
    $stmt->bind_param('sss', $title, $start_time, $end_time);
    $stmt->execute();

    // Getting the test id
    $test_id = $stmt->insert_id;

    foreach ($_POST['questions'] as $index => $question) {
        $stmt = $conn->prepare('INSERT INTO questions(test_id, question_text) VALUES(?, ?)');
        $stmt->bind_param('is', $test_id, $question);
        $stmt->execute();

        $questionId = $stmt->insert_id;

        foreach ($_POST['options'][$index] as $option_index => $option) {
            $is_correct = ($_POST['correct_option'][$index] == $option_index) ? 1 : 0;
            $stmt = $conn->prepare('INSERT INTO options(question_id, option_text, is_correct) VALUES(?, ?, ?)');
            $stmt->bind_param('isi', $questionId, $option, $is_correct);
            $stmt->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Add</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <div class="sidebar w-full lg:w-[20%] bg-[#222d32] text-white flex flex-col">
            <div class="text-center bg-blue-400 py-3">
                <a href="index.php" class="font-medium text-xl">ADMIN PANEL</a>
            </div>
            <ul class="mt-3 space-y-2 flex-grow">
                <li>
                    <a href="add.php" class="block border-b border-[#2d3c42] hover:bg-[#2d3c42] py-2 px-2">Add New Test</a>
                </li>
                <li>
                    <a href="addusers.php" class="block border-b border-[#2d3c42] hover:bg-[#2d3c42] py-2 px-2">Add Users</a>
                </li>
                <li>
                    <a href="genreport.php" class="block border-b border-[#2d3c42] hover:bg-[#2d3c42] py-2 px-2">Generate Report</a>
                </li>
            </ul>
            <div class="px-4 py-2">
                <a href="logout.php" class="bg-red-400 px-4 py-2 rounded-md">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-[#ecf0f5] p-4 lg:p-6">
            <h1 class="text-2xl font-semibold mb-4">Add New Test</h1>

            <div class="mt-3 bg-white p-6 rounded-md w-full lg:w-2/3 mx-auto">
                <form class="space-y-4" action="" method="post">
                    <h1 class="text-center font-medium text-lg mb-4">Test Details</h1>

                    <div class="space-y-3">
                        <label class="block text-gray-600">Test Title</label>
                        <input type="text" name="title" class="w-full p-2 border rounded-lg" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-gray-600">Start Time</label>
                        <input type="datetime-local" name="start_time" class="w-full p-2 border rounded-lg" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-gray-600">End Time</label>
                        <input type="datetime-local" name="end_time" class="w-full p-2 border rounded-lg" required>
                    </div>

                    <h1 class="text-center font-medium text-lg my-4">Questions</h1>

                    <div id="question-container" class="space-y-6">
                        <div class="space-y-3">
                            <label class="block text-gray-600">Question:</label>
                            <textarea class="w-full p-2 border rounded-lg h-40 resize-none" name="questions[]" placeholder="Enter question here..." required></textarea>

                            <div class="space-y-2">
                                <label class="block text-gray-600">Options:</label>

                                <div class="flex items-center space-x-2">
                                    <input type="text" name="options[0][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 1" required>
                                    <input type="radio" name="correct_option[0]" value="0" required>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="text" name="options[0][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 2" required>
                                    <input type="radio" name="correct_option[0]" value="1" required>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="text" name="options[0][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 3" required>
                                    <input type="radio" name="correct_option[0]" value="2" required>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="text" name="options[0][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 4" required>
                                    <input type="radio" name="correct_option[0]" value="3" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded-lg" onclick="addAnotherQuestion(event)">Add Another Question</button>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const questionContainer = document.getElementById('question-container');
        let questionCount = 1;

        function addAnotherQuestion(event) {
            event.preventDefault();
            questionCount++;
            const questionDiv = document.createElement('div');
            questionDiv.classList.add('space-y-3');
            questionDiv.innerHTML = `
                <label class="block text-gray-600">Question:</label>
                <textarea class="w-full p-2 border rounded-lg h-40 resize-none" name="questions[]" placeholder="Enter question here..." required></textarea>
                <div class="space-y-2">
                    <label class="block text-gray-600">Options:</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="options[${questionCount - 1}][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 1" required>
                        <input type="radio" name="correct_option[${questionCount - 1}]" value="0" required>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="options[${questionCount - 1}][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 2" required>
                        <input type="radio" name="correct_option[${questionCount - 1}]" value="1" required>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="options[${questionCount - 1}][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 3" required>
                        <input type="radio" name="correct_option[${questionCount - 1}]" value="2" required>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="options[${questionCount - 1}][]" class="flex-grow p-2 border rounded-lg" placeholder="Option 4" required>
                        <input type="radio" name="correct_option[${questionCount - 1}]" value="3" required>
                    </div>
                </div>
            `;
            questionContainer.appendChild(questionDiv);
        }
    </script>
</body>

</html>
