<?php
session_start();
$title = "Take Quiz";
include('./includes/header.php');
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include('./config/db.php');
date_default_timezone_set('Asia/Kolkata');
$time = new DateTime();
$current_time = $time->format('Y-m-d H:i:s');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $quizId = mysqli_real_escape_string($conn, $_GET['quizId']);
    $stmt = $conn->prepare("SELECT * FROM tests WHERE id = ?");
    $stmt->bind_param('i', $quizId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $start_time = new DateTime($result['start_time']);
        $end_time = new DateTime($result['end_time']);
        if ($current_time > $end_time) {
            header("Location: index.php");
            exit();
        }
        $qstmt = $conn->prepare("SELECT q.id, q.question_text, o.id AS option_id, o.option_text 
            FROM questions q 
            JOIN options o ON q.id = o.question_id 
            WHERE q.test_id = ?");
        $qstmt->bind_param('i', $quizId);
        $qstmt->execute();
        $qresult = $qstmt->get_result();

        $interval = $end_time->diff($time);
        $time_remaining = $interval->format('%H:%I:%S');

        $questions = [];
        while ($row = $qresult->fetch_assoc()) {
            if (!isset($questions[$row['id']])) {
                $questions[$row['id']] = [
                    'id' => $row['id'],
                    'question_text' => $row['question_text'],
                    'options' => []
                ];
            }
            $questions[$row['id']]['options'][] = [
                'id' => $row['option_id'],
                'text' => $row['option_text']
            ];
        }
    } else {
        header('Location: index.php');
        exit();
    }
}
?>

<div class="h-screen w-full flex items-center justify-center flex-col gap-6">
    <h1 class="text-3xl"><?php echo htmlspecialchars($result['title']); ?></h1>
    <div class="min-h-[350px] w-[90%] flex gap-10 px-5">
        <div class="flex-1 bg-white py-3 px-5">
            <?php foreach (array_values($questions) as $index => $question): ?>
                <div class="question" id="question-<?php echo $index ?>" style="display: <?php echo $index == 0 ? 'block' : 'none'; ?>;">
                    <h1 class="text-lg font-medium"><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?></h1>
                    <div class="options mt-3 flex gap-3 flex-col">
                        <?php foreach ($question['options'] as $option): ?>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $option['id']; ?>">
                                <span class="ml-2"><?php echo htmlspecialchars($option['text']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="button-group mt-3 space-x-2">
                <button type="button" onclick="clearResponse()">Clear Answer</button>
                <button type="button" onclick="previousQuestion()">Previous</button>
                <button type="button" onclick="saveAndNext()">Save & Next</button>
            </div>
        </div>
        <div class="w-[28%] bg-white p-2 px-3">
            <div class="border-b border-slate-200 pb-2 mb-3">
                <span class="text-red-500">Time Remaining</span>
                <h1 class="text-4xl font-medium" id="timer">00:00:00</h1>
            </div>
            <div class="question-grid">
                <?php foreach (array_values($questions) as $index => $question): ?>
                    <button class="w-12 h-8 bg-gray-500 text-white rounded-sm" onclick="showQuestion(<?php echo $index ?>)" id="btn-<?php echo $index ?>"><?php echo $index + 1; ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    var currentQuestion = 0;
    const totalQuestions = <?php echo count($questions); ?>;
    const timerEl = document.getElementById('timer');
    var time_remaining = "<?php echo $time_remaining; ?>";

    function parseTime(time){
        const hours = parseInt(time.split(':')[0]);
        const minutes = parseInt(time.split(':')[1]);
        const seconds = parseInt(time.split(':')[2]);
        return (hours*3600)+(minutes*60)+seconds;
    }
    var totalSeconds = parseTime(time_remaining);
    function updateTimer(){
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        timerEl.textContent=`${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`
        if(totalSeconds<=0){
            clearInterval(timerInterval);
            window.location.href = "index.php";
        }
        else
        {
            totalSeconds--;
        }
    }
    const timerInterval = setInterval(updateTimer,1000)
    updateTimer()

    function showQuestion(index) {
        document.querySelectorAll('.question').forEach(question => question.style.display = 'none');
        document.getElementById(`question-${index}`).style.display = 'block';
        currentQuestion = index;
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        }
    }

    function previousQuestion() {
        if (currentQuestion > 0) {
            showQuestion(currentQuestion - 1);
        }
    }

    function saveAndNext() {
        const question = document.getElementById(`question-${currentQuestion}`);
        const selectedOption = question.querySelector("input[type='radio']:checked");
        if (selectedOption) {
            const btn = document.getElementById(`btn-${currentQuestion}`);
            btn.classList.remove('bg-gray-500');
            btn.classList.add('bg-green-500');
        }
        nextQuestion();
    }

    function clearResponse() {
        const question = document.getElementById(`question-${currentQuestion}`);
        const selectedOption = question.querySelector("input[type='radio']:checked");
        if (selectedOption) {
            selectedOption.checked = false;
        }
        const btn = document.getElementById(`btn-${currentQuestion}`);
        btn.classList.remove('bg-green-500');
        btn.classList.add('bg-gray-500');
    }
</script>
<?php include('./includes/footer.php'); ?>
