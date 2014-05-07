<?php

	function cryptPass($input, $rounds = 9) {
	
		$salt = "";
		$saltChars = array_merge(range('A','Z'), range('a','z'), range(0,9));
		for ($i = 0; $i < 22; $i++) {
			$salt .= $saltChars[array_rand($saltChars)];
		}
		return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
	}
	
	function generate_return() {

		global $response, $success, $message;

		array_push($response, array("success" => $success, "message" => $message));
		echo json_encode($response);
	}
	
?>