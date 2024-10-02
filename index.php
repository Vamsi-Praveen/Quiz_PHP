<?php
session_start();
$title = "Quiz";
include('./includes/header.php');
?>

<?php
include('./config/db.php');
include('./utils/functions.php');
date_default_timezone_set('Asia/Kolkata');
$time = new DateTime();
$current_time = $time->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT * FROM tests ORDER BY start_time DESC");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if(isset($_SESSION['userId'])){
    $stmt_user = $conn->prepare("SELECT completed_tests,points FROM user WHERE id = ?");
    $stmt_user->bind_param('i', $_SESSION['userId']);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();
    $completed_tests = json_decode($user_data['completed_tests'], true) ?? [];
    $_SESSION['points'] = $user_data['points']  ?? 0;
    $stmt_user->close();
}
?>
<div class="container mx-auto p-4">
    <div class="fixed right-5 bottom-10 bg-red-400 h-[50px] w-[50px] rounded-full shadow-xl z-5 hidden transition items-center justify-center text-white cursor-pointer" id="scrollToTop" onclick="scrollToTop()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>
    </div>
    <div class="w-full flex flex-row items-center justify-between py-5">
        <h1 class="text-center text-3xl md:text-4xl font-medium text-gray-600">Quiz App</h1>
        <div class="flex items-center gap-5 mt-4 md:mt-0">
            <?php if(isset($_SESSION['userId'])): ?>
                <div class="flex items-center gap-2">
                    <img src="./assets/coin.png" class="object-cover w-8 h-8">
                    <h1 class="text-xl text-gray-600"><?php echo $_SESSION['points']?></h1>
                </div>
               <!--  <div>
                    <h1 class="text-lg md:text-xl text-gray-600">Welcome, <span class="font-medium"><?php echo $_SESSION['username']; ?></span></h1>
                </div> -->
            <?php endif; ?>
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
    </div>
    <div class="mt-5 space-y-5">
        <?php if($result->num_rows>0):?>
        <?php
        while($row = $result->fetch_assoc()){
            $s_time = $row['start_time'];
            $e_time = $row['end_time'];
            $start_parts = explode(" ", $s_time);
            $start_date = $start_parts[0];
            $start_time = $start_parts[1];
            $start_date_parts = explode('-', $start_date);
            $start_date_reversed = array_reverse($start_date_parts);
            $start_time = implode('-', $start_date_reversed) . ' ' . $start_time;

            $end_parts = explode(" ", $e_time);
            $end_date = $end_parts[0];
            $end_time = $end_parts[1];
            $end_date_parts = explode('-', $end_date);
            $end_date_reversed = array_reverse($end_date_parts);
            $end_time = implode('-', $end_date_reversed) . ' ' . $end_time;

            $is_active = $current_time >= $row['start_time'] && $current_time <= $row['end_time'];
            $is_completed = $current_time > $row['end_time'] || (isset($_SESSION['userId']) && in_array($row['id'], $completed_tests));

            $stmt_q = $conn->prepare("SELECT COUNT(*) AS total_questions FROM questions WHERE test_id=?");
            $stmt_q->bind_param('i', $row['id']);
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
            <div class="rounded-lg shadow-md overflow-hidden hover:shadow-lg hover:scale-2 border border-transparent hover:border-slate-300 transition">
                <div class="bg-white ">
                    <div class="px-4 py-5 flex justify-between items-center border-b border-slate-200">
                        <div class="space-y-1">
                            <h1 class="text-xl font-medium"><?php echo htmlspecialchars($row['title']); ?></h1>
                            <?php
                            if(isset($_SESSION['userId'])){
                                if(in_array($row['id'], $completed_tests)){
                                    echo '<h1>Score: <span class="font-medium text-green-500 text-xl">'.$correct_answers.'</span> / '.$total_questions.'</h1>';
                                }
                            }
                            ?>
                        </div>
                        <div class="flex md:items-center items-end gap-3 flex-col md:flex-row">
                            <?php echo $is_completed ? '<img src="assets/lock.png" alt="lock" class="h-5 w-5 mt-2">' : ''; ?>
                            <button 
                            class="<?php echo $is_active && !$is_completed ? 'bg-green-500 hover:bg-green-600' : 'bg-red-400 cursor-not-allowed'; ?> text-white font-medium py-2 px-4 rounded transition"
                            <?php if(!$is_active) echo 'disabled'; ?> 
                            <?php if($is_active && !$is_completed) echo 'onClick="window.location.href=\'take_quiz.php?quizId=' . encrypt_data($row['id']) . '\'"'; ?>
                            >
                            <?php echo $is_completed ? 'Completed' : 'Take the Test'; ?>
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
        </div>
        <?php
    }
    ?>
     <?php else:?>
            <div class="text-center text-2xl text-gray-600 min-h-[400px]">
                No tests available
            </div>
        <?php endif; ?>
</div>
</div>

<?php include('./includes/footer.php'); ?>
