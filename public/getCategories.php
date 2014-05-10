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

	//This file holds our connection properties and creates our connection to the DB
	// See http://www.php.net/manual/en/mysqli.quickstart.connections.php
	include '../includes/db.php';
	include '../includes/utils.php';
	$success = false;
	$response = array();
	$message = "Nothing to do";
	
	$sql = "select category_id, display_value from category where active = 1 order by display_value";

	// Generate the prepared statement
	if ( !($stmt = $mysqli->prepare($sql)) ) {
		$message = "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
		generate_return();
		return;
	}
	
	// Execute the prepated statement
	if (!$stmt->execute()) {
		$message = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		generate_return();
		return;
	}
	
	// Bind the results to the variables
	if (!$stmt->bind_result($categoryId, $categoryDisplay)){
		$message = "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
		generate_return();
		return;
	}
	else {
		//Buffers data
		$stmt->store_result();
		
		//Do stuff if we have data returned
		if ($stmt->num_rows > 0) {
			
			//Create an array to store our results in
			$array = array();
			//Gets rows from buffered data
			while ($stmt->fetch()){
				//put the results into an array and push them to our result array
				array_push($array, 
					array(
						"id" => $categoryId
						, "value" => $categoryDisplay
					)
				);
			}
			
			// convert the array to json and send back
			$message = $array;
			$success = true;
		} else { //Otherwise do nothing
			$message = "No data found";
			$success = true;
		}	
	}
	
	generate_return();

	close_statement();
		
	close_connection();

?>