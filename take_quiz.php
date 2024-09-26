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
   <!--  <div class="h-screen w-full flex items-center justify-center flex-col gap-8">
        <h1 class="text-4xl font-medium text-gray-600"><?php echo htmlspecialchars($result['title'])?></h1>
        <div class="min-h-[350px] w-[90%] flex gap-10">
            <div class="flex-1 bg-white rounded-lg shadow-xl p-4 flex flex-col justify-between">
                <div id="question-container">
                    <?php foreach(array_values($questions) as $index=>$question):?>
                        <div class="question" id="question-<?php echo $index?>" style="display: <?php echo $index==0? 'block' :'none'?>;">
                            <strong class="text-xl font-semibold text-gray-800 mb-4"><?php echo ($index+1).". ".htmlspecialchars($question['question_text'])?></strong>
                            <div class="options mt-3 flex flex-col gap-2">
                                <?php foreach($question['options'] as $option):?>
                                    <label class="flex items-center bg-gray-50 rounded-lg p-3 space-x-2 transition hover:bg-gray-100 cursor-pointer">
                                        <input type="radio" name="answer-<?php echo $question['id']?>" value="<?php echo $option['id']?>" class="w-4 h-4">
                                        <span class="text-gray-700"><?php echo htmlspecialchars($option['text'])?></span>
                                    </label>
                                <?php endforeach;?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
                <div class="mt-4 space-x-3">
                    <button class="text-white bg-green-400 transition hover:bg-green-500 font-semibold py-2 px-4 rounded-lg" onclick="previousQuestion()">Previous</button>
                    <button class="text-white bg-green-400 transition hover:bg-green-500 font-semibold py-2 px-4 rounded-lg" onclick="saveAndNext()">Save and Next</button>
                </div>
            </div>
            <div class="w-[30%] h-full bg-white rounded-lg p-4 shadow-xl flex flex-col justify-between sticky top-0">
               <div>
                <div class="border-b border-slate-200 pb-2 mb-2">
                    <p class="text-red-500 font-medium">Time Remaining</p>
                    <h2 class="text-4xl font-medium" id="timer">00:00:00</h2>
                </div>
                <div class="question-grid">
                    <?php foreach(array_values($questions) as $index=>$question):?>
                        <button class="bg-gray-100 px-1 py-1.5 rounded-md hover:bg-gray-200 transition font-medium" id="q-nav-<?php echo $index?>" onclick="showQuestion(<?php echo $index?>)"><?php echo $index+1?></button>
                    <?php endforeach;?>
                </div>
            </div>
            <div class="self-end mt-2">
                <button class="bg-red-500 text-white px-3 py-2 rounded-lg py-2 px-4 transition hover:bg-red-600" onclick="submittest()">Submit</button>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 bg-black hidden bg-opacity-60 flex items-center justify-center transition-opacity duration-300" id="scoremodal">
        <div class="bg-white w-full max-w-md p-3 rounded-lg shadow-xl text-center">
            <h1 class="text-3xl font-medium">Test Score</h1>
            <div role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="--value: 33"></div>
            <h2 class="text-xl mt-2"><span class="text-2xl font-medium text-green-600" id="score">0</span>&nbsp;/&nbsp;<span id="totalQ">0</span></h2>

            <a class="bg-red-400 text-white px-4 rounded-md py-2 mt-3 inline-block" href="index.php">Continue</a>
        </div>
    </div> -->
    <div class="min-h-screen w-full flex items-center justify-center flex-col gap-10 p-4">
       <div class="fixed right-5 bottom-10 bg-red-400 h-[50px] w-[50px] rounded-full shadow-xl z-5 hidden transition items-center justify-center text-white cursor-pointer" id="scrollToTop" onclick="scrollToTop()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>
    </div>
    <h1 class="text-2xl md:text-4xl font-medium text-gray-600 text-center"><?php echo htmlspecialchars($result['title'])?></h1>
    <div class="md:w-[95%] flex flex-col lg:flex-row md:gap-6 gap-5 w-full">
        <div class="flex-1 bg-white rounded-lg shadow-xl p-4 flex flex-col justify-between">
            <div id="question-container">
                <?php foreach(array_values($questions) as $index=>$question):?>
                    <div class="question" id="question-<?php echo $index?>" style="display: <?php echo $index==0? 'block' :'none'?>;">
                        <strong class="text-lg md:text-xl font-semibold text-gray-800 mb-4 block"><?php echo ($index+1).". ".htmlspecialchars($question['question_text'])?></strong>
                        <div class="options mt-3 flex flex-col gap-2">
                            <?php foreach($question['options'] as $option):?>
                                <label class="flex items-center bg-gray-50 rounded-lg p-3 space-x-2 transition hover:bg-gray-100 cursor-pointer">
                                    <input type="radio" name="answer-<?php echo $question['id']?>" value="<?php echo $option['id']?>" class="w-4 h-4">
                                    <span class="text-sm md:text-base text-gray-700"><?php echo htmlspecialchars($option['text'])?></span>
                                </label>
                            <?php endforeach;?>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <div class="mt-4 space-x-3 flex justify-center md:justify-start">
                <button class="text-white bg-green-400 transition hover:bg-green-500 font-semibold py-2 px-4 rounded-lg text-sm md:text-base" onclick="previousQuestion()">Previous</button>
                <button class="text-white bg-green-400 transition hover:bg-green-500 font-semibold py-2 px-4 rounded-lg text-sm md:text-base" onclick="saveAndNext()">Save and Next</button>
            </div>
        </div>
        <div class="w-full lg:w-[30%] bg-white rounded-lg p-4 shadow-xl flex flex-col justify-between">
         <div>
            <div class="border-b border-slate-200 pb-2 mb-2">
                <p class="text-red-500 font-medium">Time Remaining</p>
                <h2 class="text-2xl md:text-4xl font-medium" id="timer">00:00:00</h2>
            </div>
            <div class="question-grid grid grid-cols-5 gap-2">
                <?php foreach(array_values($questions) as $index=>$question):?>
                    <button class="bg-gray-100 px-1 py-1.5 rounded-md hover:bg-gray-200 transition font-medium text-sm" id="q-nav-<?php echo $index?>" onclick="showQuestion(<?php echo $index?>)"><?php echo $index+1?></button>
                <?php endforeach;?>
            </div>
        </div>
        <div class="self-end mt-2">
            <button class="bg-red-500 text-white px-3 py-2 rounded-lg transition hover:bg-red-600 w-full text-sm md:text-base" onclick="submittest()">Submit</button>
        </div>
    </div>
</div>
<div class="fixed inset-0 bg-black hidden bg-opacity-60 flex items-center justify-center transition-opacity duration-300" id="scoremodal">
    <div class="bg-white w-[90%] max-w-md p-3 rounded-lg shadow-xl text-center">
        <img src="./assets/trophy.png" class="object-cover h-[200px] w-[200px] mx-auto" id="res_image">
        <h1 class="text-2xl md:text-3xl font-medium">Test Score</h1>
        <h2 class="text-xl mt-2"><span class="text-2xl font-medium text-green-600" id="score">0</span>&nbsp;/&nbsp;<span id="totalQ">0</span></h2>

        <a class="bg-red-400 text-white px-4 rounded-md py-2 mt-3 inline-block text-sm md:text-base" href="index.php">Continue</a>
    </div>
</div>
<script>
    var currentQuestion = 0;
    const totalQuestions = <?php echo count($questions);?>;

    var time_remaining = "<?php echo $time_remaining;?>";

    var questions = <?php echo json_encode(array_values($questions)); ?>;

    var isSubmitted = false;



    const timer = document.getElementById('timer');
    const score = document.getElementById('score');
    const totalQ = document.getElementById('totalQ');
    const modal = document.getElementById('scoremodal');
    const res_image = document.getElementById('res_image');

    totalQ.textContent = totalQuestions;

    const documentTitle = document.title;
    let submitTimer;
        // visibilty API
        document.addEventListener('visibilitychange',function(){
            if(document.hidden){
                document.title = 'Please Come back...'
                submitTimer = setTimeout(function(){
                    if(!isSubmitted){
                        submittest();
                    }
                },3000)
            }else{
                clearTimeout(submitTimer)
                document.title = documentTitle;
            }
        })

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
                    isSubmitted = true;
                    score.textContent = this.responseText;
                    var scoreValue = parseInt(this.responseText);
                    modal.classList.remove('hidden');
                    if (scoreValue >= totalQuestions / 2) {
                        res_image.src = './assets/trophy.png';
                        startSchoolPrideConfetti();
                    }
                    else
                    {
                        res_image.src = "./assets/sad.png";
                        res_image.classList.remove('w-[200px]');
                        res_image.classList.remove('h-[200px]');
                        res_image.classList.add('h-[140px]');
                        res_image.classList.add('h-[140px]');
                    }
                    var percentage = (scoreValue / totalQuestions) * 100;
                    if (percentage >= 85) {
                        updateUserPoints(5);
                    } else if (percentage >= 60) {
                        updateUserPoints(3);
                    } else if (percentage < 30) {
                        updateUserPoints(1);
                    }
        }
    }
    xhr.send(`testId=<?php echo $quizId?>&action=FETCH`)
}

function updateUserPoints(points){
    var xhrUpdatePoints = new XMLHttpRequest();
    xhrUpdatePoints.open('POST', 'update_points.php', true); // Ensure you have an endpoint to update points
    xhrUpdatePoints.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhrUpdatePoints.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log('Points updated successfully');
        }
    };

    xhrUpdatePoints.send(`userId=<?php echo $_SESSION['userId']; ?>&points=${points}`);
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
        document.getElementById(`q-nav-${currentQuestion}`).classList.remove('hover:bg-gray-300');
        document.getElementById(`q-nav-${currentQuestion}`).classList.add('bg-green-400');
        document.getElementById(`q-nav-${currentQuestion}`).classList.add('hover:bg-green-500');
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