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
	$success = false;
	$response = array();
	$message = "Nothing to do";
	
	function generate_return() {

		global $response, $success, $message;

		array_push($response, array("success" => $success, "message" => $message));
		echo json_encode($response);
	}

    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'name';
    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
    $query = isset($_POST['query']) ? $_POST['query'] : false;
    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;


    if(isset($_GET['Add'])){ // this is for adding records

        $rows = $_SESSION['purchaseGrid'];
        $rows[$_GET['EmpID']] = 
        array(
            'name'=>$_GET['Name']
            , 'favorite_color'=>$_GET['FavoriteColor']
            , 'favorite_pet'=>$_GET['FavoritePet']
            , 'primary_language'=>$_GET['PrimaryLanguage']
        );
        $_SESSION['purchaseGrid'] = $rows;

    }
    elseif(isset($_GET['Edit'])){ // this is for Editing records
        $rows = $_SESSION['purchaseGrid'];
        
        unset($rows[trim($_GET['OrgEmpID'])]);  // just delete the original entry and add.
        
        $rows[$_GET['EmpID']] = 
        array(
            'name'=>$_GET['Name']
            , 'favorite_color'=>$_GET['FavoriteColor']
            , 'favorite_pet'=>$_GET['FavoritePet']
            , 'primary_language'=>$_GET['PrimaryLanguage']
        );
        $_SESSION['purchaseGrid'] = $rows;
    }
    elseif(isset($_GET['Delete'])){ // this is for removing records
        $rows = $_SESSION['purchaseGrid'];
        unset($rows[trim($_GET['Delete'])]);  // to remove the \n
        $_SESSION['purchaseGrid'] = $rows;
    }
    else{
	
		//Create an array to store our results in
		header("Content-type: application/json");
		$jsonData = array('page'=>$page,'total'=>0,'rows'=>array());
	
		$sql = "select p.purchase_id, p.purchase_date, l.display_value, c.display_value, u.user_name, p.amount, p.created_date, u2.user_name from purchase p join location l on p.location_id = l.location_id join category c on p.category_id = c.category_id join users u on p.purchaser = u.user_id join users u2 on p.created_by = u2.user_id order by p.purchase_date desc";

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
				$message = $array;
				$success = true;
			} else { //Otherwise do nothing
				$message = "No data found";
				$success = true;
			}
		}
		
		$_SESSION['purchaseGrid'] = $jsonData['rows'];
		
		$jsonData['total'] = count($jsonData['rows']);
        echo json_encode($jsonData);

		close_statement();
			
		close_connection();

}

?>