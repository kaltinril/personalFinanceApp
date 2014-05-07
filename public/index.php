<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/site.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script type="text/javascript">
	
		$(document).ready(function () {
	
			$('#reset').click( function() {
				$( '#errorMsg' ).empty();
				$( '#successMsg' ).empty();
			});
			
		});

		//Use ajax to log in
		function login(event) {

			event.preventDefault();
			
			$.ajax({
				url: "utils/login.php",
				type: 'post',
				data: $( '#loginForm' ).serialize(),
				dataType: 'json',
				async: false,
				success: function(data) {
					if (data[0].success === true) {
						document.location = "home.php";
					} else {
						$( '#errorMsg' ).html( data[0].message );
						$( '#uname' ).val("");
						$( '#pwd' ).val("");
					}
				}
			});
		}

	</script>
</head>
<body id="loginPage">
	<div id="insecure" class="insecure">
	WARNING: 
	THIS IS NOT A SECURE SITE. DO NOT USE IDENTIFIABLE INFORMATION!
	FAILURE TO HEED THIS WARNING IS DONE AT YOUR OWN RISK AND IS NOT THE RESPONSIBILITY OF THE DEVELOPER
	</div>
	<div id="errorMsg" class="errorMsg"></div>
	<div id="loginFormDiv">
	<form id="loginForm" onsubmit="login(event)">
		<label>Username:</label><input id="uname" type="text" name="username" required>
		<label>Password:</label><input id="pwd" type="password" name="password" required>
		<br/>
		<div>
			<input type="submit" value="Login">
			<input type="reset" id="reset" name="reset" value="Reset">
		</div>
	</form>
	</div>
	<a href="utils/signup.php">Create Account</a>
</body>
</html>