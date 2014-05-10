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
	
	if (!isset($_SESSION['uid'])) {
		header ("Location: index.php");
		exit;
	}
	
	header("Content-type: application/json");	

	include '../includes/db.php';
	include '../includes/utils.php';
	$success = false;
	$response = array();
	$message = "Nothing to do";
	
	if (array_key_exists("purchAmt", $_POST) && array_key_exists("purchDate", $_POST) 
		&& array_key_exists("purchLoc", $_POST) && array_key_exists("purchCat", $_POST)) {
	
		$sql = "insert into purchase (purchase_date, location_id, category_id, amount, purchaser, created_by, created_date) values (STR_TO_DATE(?,'%m/%d/%Y'),?,?,?,?,?,now())";
	
		if ( !($stmt = $mysqli->prepare($sql)) ) {
			$message = "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			generate_return();
			return;
		}
		
		$purchDate = $mysqli->real_escape_string($_POST['purchDate']);
		$purchAmt = $mysqli->real_escape_string($_POST['purchAmt']);
		$purchLoc = $mysqli->real_escape_string($_POST['purchLoc']);
		$purchCat = $mysqli->real_escape_string($_POST['purchCat']);
		
		if (!$stmt->bind_param("siidss", $purchDate, $purchLoc, $purchCat, $purchAmt, $_SESSION['user_id'], $_SESSION['user_id'])) {
			$message = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		if (!$stmt->execute()) {
			$message = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		
		$success = true;
		$message = NULL;
	
		close_statement();
			
		close_connection();
	}
	
	generate_return();
?>