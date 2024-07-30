<?php
session_start();
include('./utils/functions.php');
if(!(isset($_SESSION['userId']))){
    header("location:login.php?redirect=".getCurrentUrl());
    exit();
}
?>
<?php
include('./includes/header.php');
?>
<?php
    date_default_timezone_set('Asia/Kolkata');
    $time = new DateTime();
    $current_time = $time->format('Y-m-d H:i:s');
    include('./config/db.php');
    if($_SERVER['REQUEST_METHOD']=='GET'){
        $id = mysqli_real_escape_string($conn,$_GET['quizId']);
        $quizId = decrypt_data($id);

        $stmt_user = $conn->prepare("SELECT completed_tests FROM user WHERE id = ?");
        $stmt_user->bind_param('i', $_SESSION['userId']);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user_data = $result_user->fetch_assoc();
        $completed_tests = json_decode($user_data['completed_tests'], true) ?? [];
        if(in_array($quizId,$completed_tests)){
            header("location:index.php");
            exit();
        }
        $stmt_user->close();

        $stmt = $conn->prepare('SELECT * FROM tests WHERE id=?');
        $stmt->bind_param('i',$quizId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if($result){
            $start_time = new DateTime($result['start_time']);
            $end_time = new DateTime($result['end_time']);
            if($current_time>$end_time->format('Y-m-d H:i:s')){
                header("location:index.php");
                exit();
            }
            $interval = $end_time->diff($time);
            $hours = $interval->days * 24 + $interval->h; // Total hours including days
            $minutes = $interval->i; // Minutes
            $seconds = $interval->s; // Seconds
            $time_remaining = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $stmt = $conn->prepare('SELECT q.id, q.question_text, o.id AS option_id, o.option_text 
            FROM questions q 
            JOIN options o ON q.id = o.question_id 
            WHERE q.test_id = ?');
            $stmt->bind_param('i',$quizId);
            $stmt->execute();
            $qresult = $stmt->get_result();

            $questions = [];
            while($row = $qresult->fetch_assoc()){
                if(!(isset($questions[$row['id']]))){
                    $questions[$row['id']]=[
                        'id'=>$row['id'],
                        'question_text'=>$row['question_text'],
                        'options'=>[]
                    ];
                }
                $questions[$row['id']]['options'][]=[
                    'id'=>$row['option_id'],
                    'text'=>$row['option_text']
                ];
            }
        }
        else
        {
            header("location:index.php");
            exit();
        }
    }

?>
<div class="h-screen w-full flex items-center justify-center flex-col gap-6">
    <h1 class="text-3xl"><?php echo htmlspecialchars($result['title'])?></h1>
    <div class="min-h-[350px] w-[90%] flex gap-10">
        <div class="flex-1 bg-white rounded-sm p-3 flex flex-col justify-between">
            <div id="question-container">
                <?php foreach(array_values($questions) as $index=>$question):?>
                    <div class="question" id="question-<?php echo $index?>" style="display: <?php echo $index==0? 'block' :'none'?>;">
                        <strong class="text-lg"><?php echo ($index+1).". ".htmlspecialchars($question['question_text'])?></strong>
                        <div class="options mt-3 flex flex-col gap-2">
                            <?php foreach($question['options'] as $option):?>
                                <label>
                                    <input type="radio" name="answer-<?php echo $question['id']?>" value="<?php echo $option['id']?>">
                                    <span><?php echo htmlspecialchars($option['text'])?></span>
                                </label>
                            <?php endforeach;?>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <div class="mt-3 space-x-2 ">
                <button class="text-white bg-green-400 px-2 py-1.5 rounded-sm" onclick="previousQuestion()">Previous</button>
                <button class="text-white bg-green-400 px-2 py-1.5 rounded-sm" onclick="saveAndNext()">Save and Next</button>
            </div>
        </div>
        <div class="w-[30%] h-full bg-white rounded-sm p-3 flex flex-col justify-between">
           <div>
                <div class="border-b border-slate-200 pb-2 mb-2">
                <p class="text-red-500">Time Remaining</p>
                <h2 class="text-4xl" id="timer">00:00:00</h2>
            </div>
            <div class="question-grid">
                <?php foreach(array_values($questions) as $index=>$question):?>
                <button class="bg-gray-200 px-1 py-1.5 rounded-sm" id="q-nav-<?php echo $index?>" onclick="showQuestion(<?php echo $index?>)"><?php echo $index+1?></button>
                <?php endforeach;?>
            </div>
           </div>
            <div class="self-end mt-2">
                <button class="bg-red-400 text-white px-3 py-2 rounded-sm" onclick="submittest()">Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="h-screen w-full bg-black/70 absolute inset-0 z-10 flex items-center justify-center hidden transition" id="scoremodal">
    <div class="bg-white w-1/4 p-3 rounded-sm">
        <h1 class="text-xl font-medium">Test Score</h1>
        <h2 class="text-xl mt-2"><span class="text-2xl font-medium text-green-600" id="score">0</span>&nbsp;/&nbsp;<span id="totalQ">0</span></h2>

        <a class="bg-red-400 text-white rounded-sm px-1.5 py-2 mt-3 inline-block" href="index.php">Continue</a>
    </div>
</div>
<script>
    var currentQuestion = 0;
    const totalQuestions = <?php echo count($questions);?>;

    var time_remaining = "<?php echo $time_remaining;?>";

    var questions = <?php echo json_encode(array_values($questions)); ?>;


    console.log(questions)

    const timer = document.getElementById('timer');
    const score = document.getElementById('score');
    const totalQ = document.getElementById('totalQ');
    const modal = document.getElementById('scoremodal');

    totalQ.textContent = totalQuestions;

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
        timer.textContent=`${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`
        if(totalSeconds<=0){
            clearInterval(timerInterval);
            submittest();
            window.location.href = "index.php";
        }
        else
        {
            totalSeconds--;
        }
    }
    const timerInterval = setInterval(updateTimer,1000)
    updateTimer()

    function submittest(){
        clearInterval(timerInterval);
        var xhr = new XMLHttpRequest();
        xhr.open('POST','quiz_ajax.php',true);
        xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        xhr.onreadystatechange = function(){
            if(this.readyState==4 && this.status==200){
                score.textContent = this.responseText;
                modal.classList.remove('hidden');
            }
        }
        xhr.send(`testId=<?php echo $quizId?>&action=FETCH`)
    }

   function showQuestion(index) {
        document.querySelectorAll('.question').forEach(question => question.style.display = 'none');
        document.getElementById(`question-${index}`).style.display = 'block';
        currentQuestion = index;
    }

    function saveAndNext() {
        const selectedQuestion = document.getElementById(`question-${currentQuestion}`);
        const selectedOption = selectedQuestion.querySelector('input[type="radio"]:checked');
        if(selectedOption){
            var xhr = new XMLHttpRequest();
            xhr.open('POST','quiz_ajax.php',true);
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
            xhr.onreadystatechange = function(){
                if(xhr.readyState==4 && xhr.status ==200){
                    console.log('answer saved');
                }
            }
            xhr.send(`questionId=${questions[currentQuestion]?.id}&selectedOptionId=${selectedOption.value}&testId=<?php echo $quizId?>&action=INSERT`)
            document.getElementById(`q-nav-${currentQuestion}`).classList.remove('bg-gray-200');
            document.getElementById(`q-nav-${currentQuestion}`).classList.add('bg-green-400');
            document.getElementById(`q-nav-${currentQuestion}`).classList.add('text-white');
        }
        if (currentQuestion < totalQuestions-1) {
            showQuestion(currentQuestion + 1);
        }
    }

    function previousQuestion() {
        if (currentQuestion > 0) {
            showQuestion(currentQuestion - 1);
        }
    }

</script>
<?php
include('./includes/footer.php');
?>