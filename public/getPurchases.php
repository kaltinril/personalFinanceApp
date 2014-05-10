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

	//This file holds our connection properties and creates our connection to the DB
	// See http://www.php.net/manual/en/mysqli.quickstart.connections.php
	include '../includes/db.php';
	include '../includes/utils.php';
	$success = false;
	$response = array();
	$message = "Nothing to do";
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'name';
    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;
	
	//TODO this is horrible but at least it works for now.  Find other ways to do this!! (Maybe do it client side or use a routine)
	$resultsSQL = "select p.purchase_id as purchase_id, p.purchase_date, l.display_value as purchase_location, c.display_value as purchase_category, u.user_name, p.amount as purchase_price, p.created_date, u2.user_name ".
			"from purchase p join location l on p.location_id = l.location_id join category c on p.category_id = c.category_id join users u ".
			"on p.purchaser = u.user_id join users u2 on p.created_by = u2.user_id order by ".$mysqli->real_escape_string($sortname)." ".$mysqli->real_escape_string($sortorder)." LIMIT ?, ?";
			
	$rowCountSQL = "select COUNT(p.purchase_id) ".
			"from purchase p join location l on p.location_id = l.location_id join category c on p.category_id = c.category_id join users u ".
			"on p.purchaser = u.user_id join users u2 on p.created_by = u2.user_id";
			
	//error_log($resultsSQL);
	
	$totalRows = 0;
	
	//Create an array to store our results in
	$jsonData = array('page'=>$page,'total'=>0,'rows'=>array());
	
	// Generate the prepared statement
	if ( !($stmt = $mysqli->prepare($rowCountSQL)) ) {
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
	if (!$stmt->bind_result($total)){
		$message = "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
		generate_return();
		return;
	}
	else {
		//Buffers data
		$stmt->store_result();
		
		//Do stuff if we have data returned
		while ($stmt->fetch()){
			$totalRows = $total;
			break;
		}
		
		close_statement();
	}
	
	if ($totalRows > 0) {
	
		$offset = ($rp * ($page-1)); //value is 1 based but need 0 based
		
		//error_log($offset);
		//error_log($orderBy);
		//error_log($rp);
		
		// Generate the prepared statement
		if ( !($stmt = $mysqli->prepare($resultsSQL)) ) {
			$message = "prepare failed: (" . $mysqli->errno . ")" . $mysqli->error;
			generate_return();
			return;
		}
		
		if (!$stmt->bind_param("ii", $offset, $rp)) {
			$message = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
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
		if (!$stmt->bind_result($purchId, $purchDt, $purchLoc, $purchCat, $purchUser, $purchAmt, $createDt, $createBy)){
			$message = "Error binding result: (" . $stmt->errno . ") " . $stmt->error;
			generate_return();
			return;
		}
		else {
			//Buffers data
			$stmt->store_result();
			
			//Do stuff if we have data returned
			if ($stmt->num_rows > 0) {
				
				$rowNum = 0;
					
				//Gets rows from buffered data
				while ($stmt->fetch()){
					//put the results into an array and push them to our result array
					array_push($jsonData['rows'], 
						array('id' => $rowNum++,
							'cell'=>array(
								'purchase_id'       => $purchId,
								'purchase_date'     => $purchDt,
								'purchase_location' => $purchLoc,
								'purchase_category' => $purchCat,
								'purchase_price'    => $purchAmt
							)
						)
					);
				}
				
				// convert the array to json and send back
				$success = true;
			} else { //Otherwise do nothing
				$message = "No data found";
				$success = true;
			}
		}
		
		//$_SESSION['purchaseGrid'] = $jsonData['rows'];
			
		$jsonData['total'] = $totalRows;
		
	}

	close_statement();
		
	close_connection();
	
	echo json_encode($jsonData);

?>