<?php
session_start();
$title = "Quiz";
include('./includes/header.php');
// if(!isset($_SESSION['userId'])){
//     header("location:login.php");
//     exit();
// }
?>

<?php
// $userId = $_SESSION['userId'];
include('./config/db.php');
include('./utils/functions.php');
date_default_timezone_set('Asia/Kolkata');
$time = new DateTime();
$current_time=$time->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT * FROM tests ORDER BY start_time DESC");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if(isset($_SESSION['userId'])){
    $stmt_user = $conn->prepare("SELECT completed_tests FROM user WHERE id = ?");
    $stmt_user->bind_param('i', $_SESSION['userId']);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();
    $completed_tests = json_decode($user_data['completed_tests'], true) ?? [];
    $stmt_user->close();
}
?>

<div class="container">
   <div class="flex items-center justify-between py-5">
    <h1 class="text-center text-4xl font-medium text-gray-600">Quiz App</h1>
    <?php
    if(isset($_SESSION['userId'])){
        echo '<a class="bg-red-400 hover:bg-red-500 px-5 py-2 text-white rounded-md font-medium" href="logout.php">Logout</a>';
    }
    else
    {
        echo '<a class="bg-red-400 hover:bg-red-500 px-5 py-2 text-white rounded-md font-medium" href="login.php">Login</a>';
    }

    ?>
</div>
<div class="mt-5 space-y-5">
    <!-- <h1 class="text-center text-xl">No tests</h1> -->
    <?php
    while($row = $result->fetch_assoc()){
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $is_active = $current_time >= $start_time && $current_time <= $end_time;
        $is_completed = $current_time > $end_time || ( isset($_SESSION['userId']) && in_array($row['id'], $completed_tests));

        $stmt_q = $conn->prepare("SELECT COUNT(*) AS total_questions FROM questions WHERE test_id=?");
        $stmt_q->bind_param('i',$row['id']);
        $stmt_q->execute();
        $result_questions = $stmt_q->get_result()->fetch_assoc();
        $total_questions = $result_questions['total_questions'];

        if(isset($_SESSION['userId'])){
            $stmt_correct = $conn->prepare("SELECT COUNT(*) AS correct_answers FROM user_responses WHERE user_id = ? AND test_id = ? AND is_correct = 1");
            $stmt_correct->bind_param('ii', $_SESSION['userId'], $row['id']);
            $stmt_correct->execute();
            $result_correct = $stmt_correct->get_result()->fetch_assoc();
            $correct_answers = $result_correct['correct_answers'];
            $stmt_correct->close();
        }
        ?>
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
         <div class="px-4 py-5 flex justify-between items-center border-b border-slate-200">
            <h1 class="text-xl font-medium"><?php echo htmlspecialchars($row['title']); ?></h1>
            <?php
            if(isset($_SESSION['userId'])){
               if(in_array($row['id'],$completed_tests)){
                echo '<h1>Score:&nbsp;<span class="font-medium text-green-600 text-lg">'.$correct_answers.'</span>&nbsp;/&nbsp;'.$total_questions.'</h1>';
            }
        }
        ?>
        <div class="flex items-center gap-3">
            <?php echo $is_completed ? '<img src="assets/lock.png" alt="lock" class="h-5 w-5 mt-2">' :'';?>
            <button 
            class="<?php echo $is_active && !$is_completed ? 'bg-green-500 hover:bg-green-600' : 'bg-red-400 cursor-not-allowed'; ?> text-white font-medium py-2 px-4 rounded transition"
            <?php if(!$is_active) echo 'disabled'; ?> 
            <?php if($is_active) echo 'onClick="window.location.href=\'take_quiz.php?quizId=' . encrypt_data($row['id']) . '\'"'; ?>
            >
            <?php echo $is_completed ? 'Completed' : 'Take Test'; ?>
        </button>
    </div>
</div>
<div class="border-t border-gray-200">
    <dl>
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Start time</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($start_time); ?></dd>
        </div>
        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">End time</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($end_time); ?></dd>
        </div>
    </dl>
</div>
</div>

<?php
}
?>
</div>
</div>

<?php include('./includes/footer.php'); ?>
