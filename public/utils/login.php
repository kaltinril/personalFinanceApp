<?php session_start();

	include '../../includes/db.php';
	include '../../includes/storeLogin.php';
	include '../../includes/utils.php';
	$response = array();
	$message = NULL;
	$success = false;
	
	if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST)) {
	
		if ( !($stmt = $mysqli->prepare("select user_id, passwd from users where user_name = ?")) ) {
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
		if (!$stmt->bind_result($user_id, $hash_pwd)){
			$message .= "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		else {
			//Buffers data
			$stmt->store_result();
			
			if ($stmt->num_rows > 0) {
				
				//Gets rows from buffered data
				while ($stmt->fetch()){
				
					if (crypt($pwd, $hash_pwd) == $hash_pwd) {
						$_SESSION['user_id'] = htmlspecialchars($user_id);
						$_SESSION['username'] = $uname;
						
						$_SESSION['uid'] = 1;

						$success = true;
						
						storeLoginAttempt();
				
					} else {
						$message .= "Invalid username or password";
					}
					
					break;					
				}

			} else {
				$message .= "Invalid username or password";
			}
			
			close_statement();
			close_connection();
		}
		
	}

	generate_return();

?>