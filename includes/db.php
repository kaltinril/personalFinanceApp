<?php session_start();
	//ini_set('display_errors', 'On');
	$dbhost = 'localhost';
	$dbname = '';
	$dbuser = '';
	$dbpass = '';

	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	function close_connection() {

		global $mysqli;

		$mysqli->close();
	}
	
	function close_statement() {
	
		global $stmt;

		$stmt->free_result();
		//Deallocates statement handle
		$stmt->close();
	
	}

?>