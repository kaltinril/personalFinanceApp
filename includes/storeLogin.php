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

	function storeLoginAttempt() {
	
		global $mysqli;
		global $message;
	
		if ( !($stmt = $mysqli->prepare("INSERT INTO sessions (user_id, session_id, login_date) values (?, ?, NOW())")) ) {
			//$message .= "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			//generate_return();
			return;
		}
		
		$uid = $mysqli->real_escape_string($_SESSION['user_id']);
		
		if (!$stmt->bind_param("ss", $uid, session_id())) {
			//$message .= "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			//generate_return();
			return;
		}
		
		if (!$stmt->execute()) {
			//$message .= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			//generate_return();
			return;
		}
		
		return;
	
	}
	
	function isValidSession() {
	
		global $mysqli;
		global $message;
	
		if ( !($stmt = $mysqli->prepare("SELECT NOW() - login_date FROM sessions WHERE user_id = ? and session_id = ?")) ) {
			//$message .= "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			//generate_return();
			return false;
		}
		
		$uid = $mysqli->real_escape_string($_SESSION['user_id']);
		
		if (!$stmt->bind_param("ss", $uid, session_id())) {
			//$message .= "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			//generate_return();
			return false;
		}
		
		if (!$stmt->execute()) {
			//$message .= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			//generate_return();
			return false;
		}
		
		// One bound paramater for each thing selected in the same order
		if (!$stmt->bind_result($session_len)){
			//$message .= "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
			//generate_return();
			return false;
		}
		else {
			//Buffers data
			$stmt->store_result();
			
			if ($stmt->num_rows > 0) {
				
				//Gets rows from buffered data
				while ($stmt->fetch()){
					$ses = htmlspecialchars($session_len);
					break;
				}

				//Sessions cannot be older than x seconds
				if ($ses < 900) {
					close_statement();
					close_connection();
					return true;
				}

			}
			
			close_statement();
			close_connection();
		}
		
		return false;
	
	}

?>