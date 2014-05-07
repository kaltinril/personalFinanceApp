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