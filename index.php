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
include('./config/db.php');
date_default_timezone_set('Asia/Kolkata');
$time = new DateTime();
$current_time=$time->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT * FROM tests ORDER BY start_time DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1 class="text-center text-2xl">Quiz App</h1>
    <div class="mt-3">
        <!-- <h1 class="text-center text-xl">No tests</h1> -->
        <?php
        while($row = $result->fetch_assoc()){
            $start_time = $row['start_time'];
            $end_time = $row['end_time'];
            $is_active = $current_time >= $start_time && $current_time <= $end_time;
            $is_completed = $current_time > $end_time;
            ?>
            <div class="border p-3 mb-2">
                <h1 class="text-lg font-medium"><?php echo htmlspecialchars($row['title']); ?></h1>
                <h2>Start time: <span class="ml-1"><?php echo htmlspecialchars($start_time); ?></span></h2>
                <h2>End time: <span class="ml-1"><?php echo htmlspecialchars($end_time); ?></span></h2>
                <button class="<?php echo $is_active ? 'bg-green-400' : 'bg-red-400 cursor-not-allowed'; ?> text-white p-1.5 mt-2" <?php if(!$is_active) echo 'disabled'; ?> <?php if($is_active) echo 'onClick="window.location.href=\'take_quiz.php?quizId=' . $row['id'] . '\'"'; ?>>
                    <?php echo $is_completed ? 'Completed' : 'Take Test'; ?>
                </button>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include('./includes/footer.php'); ?>
