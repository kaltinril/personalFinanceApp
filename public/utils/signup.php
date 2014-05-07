<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Signup</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/site.css">
	<link rel="stylesheet" type="text/css" href="../../lib/tooltipster-3.2.3/css/tooltipster.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../lib/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../../lib/tooltipster-3.2.3/js/jquery.tooltipster.min.js"></script>
	<script type="text/javascript">
	
	    $(document).ready(function () {
		
			$.validator.addMethod(
				"regex",
				function (value, element, regexp) {
					var re = new RegExp(regexp);
					return re.test(value);
				},
				"Please enter a valid password"
			);
			
			$('#signupForm input').tooltipster({
				trigger: 'custom',
				onlyOne: false,
				position: 'right'
			});
			
			$('#reset').click( function() {
				$(' #pwd ').tooltipster('hide');
				$(' #uname ').tooltipster('hide');
				$( '#errorMsg' ).empty();
				$( '#successMsg' ).empty();
			});
			
			$('#submit').click( function() {
			
				$( '#errorMsg' ).empty();
				$( '#successMsg' ).empty();
			
				$('#signupForm').validate({
					rules: {
						username: {
							required: true,
							minlength: 4,
							maxlength: 50
						},
						password: {
							required: true,
							minlength: 8,
							maxlength: 50,
							regex: /((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,50})/gm
						}
					},
					messages: {
						name: {
							required: "Username is a required field",
							minlength: "Username minimum length is 4",
							maxlength: "Username maximum length is 50"
						},
						password: {
							required: "Password is a required field",
							minlength: "Password minimum length is 8",
							maxlength: "Password maximum length is 50"
						}
					},
					submitHandler: function(form) {
					
						$.ajax({
							url: "createUser.php",
							type: 'post',
							data: $( '#signupForm' ).serialize(),
							dataType: 'json',
							async: false,
							success: function(data) {
								if (data[0].success === true) {
									//document.location = "index.html";
									$( '#uname' ).val("");
									$( '#pwd' ).val("");
									$( '#successMsg' ).html( 'Account Created Successfully. Please <a href="../index.php">login</a>' );
								} else {
									$( '#errorMsg' ).html( data[0].message );
								}
							}
						});

					},
					errorPlacement: function (error, element) {
						$(element).tooltipster('update', $(error).text());
						$(element).tooltipster('show');
					},
					success: function (label, element) {
						$(element).tooltipster('hide');
					}
				});
			});
		});
	
	</script>
</head>
<body id="signupPage">
	<div id="errorMsg" class="errorMsg"></div>
	<div id="successMsg" class="successMsg"></div>
	<form id="signupForm">
		<div><label>Username:</label><input id="uname" type="text" name="username"></div>
		<p>Username Requirements: <br/>
			4-50 Characters<br/>
		</p>
		<div><label>Password:</label><input id="pwd" type="password" name="password"></div>
		<p>Password Requirements: <br/>
			8-50 Characters<br/>
			Minimum 1 capital letter<br/>
			Minimum 1 lowercase letter <br/>
			Minimum 1 number<br/>
			no special characters<br/>
		</p>
		<br/>
		<div>
			<input type="submit" id="submit" name="submit" value="Create">
			<input type="reset" id="reset" name="reset" value="Reset">
		</div>
	</form>
</body>
</html>