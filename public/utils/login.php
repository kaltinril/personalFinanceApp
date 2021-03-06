<?php 

/*
    personalFinanceApp - https://github.com/frigidplanet/personalFinanceApp
    Copyright (C) 2014 Jeremy Harris

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


	session_start();

	include '../../includes/db.php';
	include '../../includes/storeLogin.php';
	include '../../includes/utils.php';
	$response = array();
	$message = NULL;
	$success = false;
	
	if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST)) {
	
		if ( !($stmt = $mysqli->prepare("select user_id, password from users where user_name = ?")) ) {
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
