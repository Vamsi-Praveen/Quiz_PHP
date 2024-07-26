<?php
session_start();
if(!(isset($_SESSION['userId']))){
    header("location:login.php");
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
        $quizId = mysqli_real_escape_string($conn,$_GET['quizId']);

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
            $time_remaining = $end_time->diff($time)->format('Y-m-d H:i:s');
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
                <h2 class="text-4xl">00:00:00</h2>
            </div>
            <div class="question-grid">
                <?php foreach(array_values($questions) as $index=>$question):?>
                <button class="bg-gray-200 px-1 py-1.5 rounded-sm" onclick="showQuestion(<?php echo $index?>)"><?php echo $index+1?></button>
                <?php endforeach;?>
            </div>
           </div>
            <div class="self-end mt-2">
                <button class="bg-red-400 text-white px-3 py-2 rounded-sm">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
     var currentQuestion = 0;
    const totalQuestions = <?php echo count($questions);?>;

   function showQuestion(index) {
        document.querySelectorAll('.question').forEach(question => question.style.display = 'none');
        document.getElementById(`question-${index}`).style.display = 'block';
        currentQuestion = index;
    }

    function saveAndNext() {
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