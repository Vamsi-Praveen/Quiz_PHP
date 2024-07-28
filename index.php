<?php
session_start();
$title = "Quiz";
include('./includes/header.php');
if(!isset($_SESSION['userId'])){
    header("location:login.php");
    exit();
}
?>

<?php
$userId = $_SESSION['userId'];
include('./config/db.php');
include('./utils/functions.php');
date_default_timezone_set('Asia/Kolkata');
$time = new DateTime();
$current_time=$time->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT * FROM tests ORDER BY start_time DESC");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$stmt_user = $conn->prepare("SELECT completed_tests FROM user WHERE id = ?");
$stmt_user->bind_param('i', $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$completed_tests = json_decode($user_data['completed_tests'], true) ?? [];
$stmt_user->close();
?>

<div class="container">
   <div class="flex items-center justify-between">
        <h1 class="text-center text-2xl">Quiz App</h1>
        <a class="bg-red-400 px-3 py-2 text-white rounded-sm" href="logout.php">Logout</a>
   </div>
    <div class="mt-5">
        <!-- <h1 class="text-center text-xl">No tests</h1> -->
        <?php
        while($row = $result->fetch_assoc()){
            $start_time = $row['start_time'];
            $end_time = $row['end_time'];
            $is_active = $current_time >= $start_time && $current_time <= $end_time;
            $is_completed = $current_time > $end_time || in_array($row['id'], $completed_tests);

            $stmt_q = $conn->prepare("SELECT COUNT(*) AS total_questions FROM questions WHERE test_id=?");
            $stmt_q->bind_param('i',$row['id']);
            $stmt_q->execute();
            $result_questions = $stmt_q->get_result()->fetch_assoc();
            $total_questions = $result_questions['total_questions'];

             $stmt_correct = $conn->prepare("SELECT COUNT(*) AS correct_answers FROM user_responses WHERE user_id = ? AND test_id = ? AND is_correct = 1");
            $stmt_correct->bind_param('ii', $userId, $row['id']);
            $stmt_correct->execute();
            $result_correct = $stmt_correct->get_result()->fetch_assoc();
            $correct_answers = $result_correct['correct_answers'];
            $stmt_correct->close();
            ?>
            <div class="border border-slate-100 p-3 mb-2 bg-white rounded-sm shadow-sm w-1/2 flex justify-between items-center">
               <div>
                    <h1 class="text-lg font-medium"><?php echo htmlspecialchars($row['title']); ?></h1>
                    <?php
                        if(in_array($row['id'],$completed_tests)){
                            echo '<h1>Score:&nbsp;<span class="font-medium text-green-600 text-lg">'.$correct_answers.'</span>&nbsp;/&nbsp;'.$total_questions.'</h1>';
                        }

                    ?>
                    <h2>Start time: <span class="ml-1"><?php echo htmlspecialchars($start_time); ?></span></h2>
                    <h2>End time: <span class="ml-1"><?php echo htmlspecialchars($end_time); ?></span></h2>
               </div>
                <div class="flex items-end flex-col">
                    <?php echo $is_completed ? '<img src="assets/lock.png" alt="lock" class="h-4 w-4">' :'';?>
                    <button class="<?php echo $is_active && !$is_completed ? 'bg-green-400' : 'bg-red-400 cursor-not-allowed'; ?> text-white p-1.5 mt-2 rounded-sm" <?php if(!$is_active) echo 'disabled'; ?> <?php if($is_active) echo 'onClick="window.location.href=\'take_quiz.php?quizId=' . encrypt_data($row['id']) . '\'"'; ?>>
                    <?php echo $is_completed ? 'Completed' : 'Take Test'; ?>
                </button>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include('./includes/footer.php'); ?>
