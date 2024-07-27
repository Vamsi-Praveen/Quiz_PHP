<?php
	session_start();
	include('./config/db.php');
	$userId = $_SESSION['userId'];
	if($_SERVER['REQUEST_METHOD']=='POST'){
		$questionId = isset($_POST['questionId']) ? mysqli_real_escape_string($conn,$_POST['questionId']) : '';
		$selectedOptionId = isset($_POST['selectedOptionId']) ? mysqli_real_escape_string($conn,$_POST['selectedOptionId']) : '';
		$testId = mysqli_real_escape_string($conn,$_POST['testId']);
		$action = mysqli_real_escape_string($conn,$_POST['action']);

		if($action == 'INSERT'){
			$stmt = $conn->prepare('SELECT * FROM options WHERE question_id = ? AND id = ?');
			$stmt->bind_param('ii',$questionId,$selectedOptionId);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
			$is_correct = $result['is_correct'];
			$stmt->close();

			$stmt = $conn->prepare('INSERT INTO user_responses(user_id,test_id,question_id,selected_option_id,is_correct) VALUES(?,?,?,?,?)');

			$stmt->bind_param('iiiii',$userId,$testId,$questionId,$selectedOptionId,$is_correct);
			$stmt->execute();
			echo $stmt->insert_id;
			$stmt->close();
		}
		elseif($action == 'FETCH') {
	        $stmt = $conn->prepare('SELECT COUNT(*) as correct FROM user_responses WHERE is_correct = 1 AND user_id = ? AND test_id = ?');
	        $stmt->bind_param('ii', $userId, $testId);
	        $stmt->execute();
	        $result = $stmt->get_result()->fetch_assoc();
	        echo $result['correct'];
	        $stmt->close();

	        // Fetch completed tests for the user
	        $stmt_user = $conn->prepare("SELECT completed_tests FROM user WHERE id = ?");
	        $stmt_user->bind_param('i', $userId);
	        $stmt_user->execute();
	        $result_user = $stmt_user->get_result();
	        $user_data = $result_user->fetch_assoc();
	        $completed_tests = json_decode($user_data['completed_tests'], true) ?? [];

	        // Add the current test to completed tests if not already there
	        if (!in_array($testId, $completed_tests)) {
	            $completed_tests[] = $testId;
	            $completed_tests_json = json_encode($completed_tests);

	            $stmt_update = $conn->prepare("UPDATE user SET completed_tests = ? WHERE id = ?");
	            $stmt_update->bind_param('si', $completed_tests_json, $userId);
	            $stmt_update->execute();
	            $stmt_update->close();
        	}
   		 }

		
		
	}


?>