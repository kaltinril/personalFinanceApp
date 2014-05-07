<?php 

	session_start();
	
	include '../../includes/db.php';
	include '../../includes/utils.php';
	$response = array();
	$message = NULL;
	$success = false;

	if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST)) {
	
		if ( !($stmt = $mysqli->prepare("select user_id from users where user_name = ?")) ) {
			$message .= "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			generate_return();
			return;
		}
		
		$uname = $mysqli->real_escape_string($_POST['username']);
		$pwd = $mysqli->real_escape_string($_POST['password']);
		
		if (!$stmt->bind_param("s", $uname)) {
			$message .= "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		if (!$stmt->execute()) {
			$message .= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		// One bound paramater for each thing selected in the same order
		if (!$stmt->bind_result($user_id)){
			$message .= "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		else {
			//Buffers data
			$stmt->store_result();
			
			if ($stmt->num_rows > 0) {
				$message .= "Username is not available, please choose another.";
				
				close_statement();
				close_connection();
				
				generate_return();
				
				return;
				
			}
			
			close_statement();
		}
		
		$hash_pwd = cryptPass($pwd);
		
		if ( !($stmt = $mysqli->prepare("insert into users (user_name, passwd) values (?, ?)")) ) {
			$message .= "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			generate_return();
			return;
		}
		
		if (!$stmt->bind_param("ss", $uname, $hash_pwd)) {
			$message .= "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		if (!$stmt->execute()) {
			$message .= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		if ($stmt->affected_rows == 1) {
			$message .= "Account created successfully";
			$success = true;
		} else {
			$message .= "Error creating account. Please try again.";
		}
		
	}
	
	close_statement();
	close_connection();
				
	generate_return();
?>