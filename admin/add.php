<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    header("Location: login.php");
    exit();
}
?>
<?php
    include('../config/db.php');

    if($_SERVER['REQUEST_METHOD']=='POST'){
        $title = $_POST['title'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $stmt = $conn->prepare('INSERT INTO tests(title,start_time,end_time) VALUES(?,?,?)');
        $stmt->bind_param('sss',$title,$start_time,$end_time);
        $stmt->execute();
        //getting the test id 
        $test_id = $stmt->insert_id;

        foreach ($_POST['questions'] as $index => $question) {
            $stmt = $conn->prepare('INSERT INTO questions(test_id,question_text) VALUES(?,?)');
            $stmt->bind_param('is',$test_id,$question);
            $stmt->execute();

            $questionId = $stmt->insert_id;

            foreach($_POST['options'][$index] as $option_index=>$option){
                $is_correct = ($_POST['correct_option'][$index] == $option_index) ? 1:0;
                $stmt = $conn->prepare('INSERT INTO options(question_id,option_text,is_correct) VALUES(?,?,?)');
                $stmt->bind_param('isi',$questionId,$option,$is_correct);
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
    <div class="min-h-screen w-full flex">
        <div class="sidebar w-[20%] bg-[#222d32] text-white/90">
            <div class="text-center bg-blue-400 py-3">
                <a href="index.php" class="font-medium text-xl">ADMIN PANEL</a>
            </div>
            <ul class="mt-3 space-y-2">
                <li class="border-b border-[#2d3c42] cursor-pointer hover:bg-[#2d3c42] py-2 pl-2">
                    <a href="add.php">Add New Test</a>
                </li>
            </ul>
        </div>
        <div class="content flex-1 bg-[#ecf0f5]">
            <div class="w-full h-full p-3 px-5">
                <h1 class="text-2xl font-semibold mb-4">Add New Test</h1>
                <div class="mt-3 bg-white p-6 rounded-md w-1/2">
                    <form class="space-y-4 px-2" action="" method="post">
                        <h1 class="text-center my-2 font-medium">Test Details</h1>
                        <div class="space-x-3 flex">
                            <label class="w-1/4">Test Title</label>
                            <input type="text" name="title" class="border w-3/4 p-2 rounded" required>
                        </div>
                        <div class="space-x-3 flex">
                            <label class="w-1/4">Start Time</label>
                            <input type="datetime-local" name="start_time" class="border w-3/4 p-2 rounded" required>
                        </div>
                        <div class="space-x-3 flex">
                            <label class="w-1/4">End Time</label>
                            <input type="datetime-local" name="end_time" class="border w-3/4 p-2 rounded" required>
                        </div>
                        <h1 class="text-center my-2 font-medium">Questions</h1>
                        <div id="question-container">
                            <div class="space-y-2">
                                <label>Question:</label>
                                <textarea class="border resize-none w-full h-40 px-1 rounded" name="questions[]" placeholder="Enter question here..." required></textarea>
                                <div class="space-y-2">
                                    <label>Options:</label><br>
                                    <input type="text" name="options[0][]" class="border p-1 rounded" placeholder="Option 1" required>
                                    <input type="radio" name="correct_option[0]" value="0" required><br>
                                    <input type="text" name="options[0][]" class="border p-1 rounded" placeholder="Option 2" required>
                                    <input type="radio" name="correct_option[0]" value="1" required><br>
                                    <input type="text" name="options[0][]" class="border p-1 rounded" placeholder="Option 3" required>
                                    <input type="radio" name="correct_option[0]" value="2" required><br>
                                    <input type="text" name="options[0][]" class="border p-1 rounded" placeholder="Option 4" required>
                                    <input type="radio" name="correct_option[0]" value="3" required><br>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="bg-blue-400 px-3 py-1 text-white rounded hover:bg-blue-500" onclick="addAnotherQuestion(event)">Add another Question</button>
                        </div>
                        <div>
                            <button type="submit" class="bg-green-500 px-3 py-1 text-white rounded hover:bg-green-600">Submit</button>
                        </div>
                    </form>
                </div>
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
            questionDiv.classList.add('space-y-2');
            questionDiv.innerHTML = `
                <label>Question:</label>
                <textarea class="border resize-none w-full h-40 px-1 rounded" name="questions[]" placeholder="Enter question here..." required></textarea>
                <div class="space-y-2">
                    <label>Options:</label><br>
                    <input type="text" name="options[${questionCount-1}][]" class="border p-1 rounded" placeholder="Option 1" required>
                    <input type="radio" name="correct_option[${questionCount-1}]" value="0" required><br>
                    <input type="text" name="options[${questionCount-1}][]" class="border p-1 rounded" placeholder="Option 2" required>
                    <input type="radio" name="correct_option[${questionCount-1}]" value="1" required><br>
                    <input type="text" name="options[${questionCount-1}][]" class="border p-1 rounded" placeholder="Option 3" required>
                    <input type="radio" name="correct_option[${questionCount-1}]" value="2" required><br>
                    <input type="text" name="options[${questionCount-1}][]" class="border p-1 rounded" placeholder="Option 4" required>
                    <input type="radio" name="correct_option[${questionCount-1}]" value="3" required><br>
                </div>
            `;
            questionContainer.appendChild(questionDiv);
        }
    </script>
</body>
</html>
