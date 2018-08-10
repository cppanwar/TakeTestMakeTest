<?php
	$page_title = 'Sign Up';
	$error = array();
	require_once('include/constants.php');
	require_once('include/sessionstart.php');
	require_once('include/functions.php');
	require_once('include/connectvars.php');
	require_once('include/htmltemplate.php');
	?>
	
	<script type="text/javascript">
			var root = '<?php echo ROOT; ?>';
			function refreshCaptcha () {
  				document.getElementById('captcha').src = root+'/captcha.php?' + Math.random();
  				document.getElementById('captcha_code').value = '';
			}
			window.onload = function () {
				//Captcha code
			 refresh = document.getElementById("refresh");
			 refresh.onclick=refreshCaptcha;
		};
	</script>
	
	<?php
	require_once('include/header.php');
	require_once('include/navigation.php');
	//echo '<section>';
	$error_flag = 0;
	if(!isLoggedIn()) 
	{
			$gender = 'M';
		if(isset($_POST['submit'])) 
		{
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				//Grab the data from the POST
		 		//Removing threats from SQL injection and XSS
				$password1 = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['password1'])));
			 	$email 	  = mysqli_real_escape_string($dbc, strtolower( strip_tags(trim($_POST['email']))));
    			$password2 = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['password2'])));
				$firstname = mysqli_real_escape_string($dbc, strtolower( strip_tags(trim($_POST['firstname']))));
		 		$lastname  = mysqli_real_escape_string($dbc, strtolower( strip_tags(trim($_POST['lastname']))));
		 		if(isset($_POST['gender'])) 
		 		$gender    = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['gender'])));
		 		
		 		$phone	  = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['phone'])));
		 		
			if(captchaCheck($_POST['captcha_code'])) 
			{
		 		
		 		
		 		//Following IF statements check for empty fields.
		 		if( empty($password1) || empty($password2) ) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Password field is empty.';
		 		}				
		 		if(empty($email)) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Email field is empty.';
		 		}				
				if(empty($firstname)) 
				{
					$error_flag = 1;
					$error[] = 'You must enter your First Name.';
				}
				if(empty($lastname)) 
				{
					$error_flag = 1;
					$error[] = 'You must enter your Last Name.';
				}
				if(empty($gender)) 
				{
					$error_flag = 1;
					$error[] = 'Select your Gender.';
				}
				if(empty($phone)) 
				{
					$error_flag = 1;
					$error[] = 'Enter your Mobile Number.';
				}
				//Following IF statements validate form data
				if($error_flag == 0) 
				{
					if(strlen($password1) < MIN_PASSWORD_LENGTH || strlen($password1)> MAX_PASSWORD_LENGTH || $password1 != $password2) 
					{
						$error_flag = 1;
						$error[] = 'Password must contain 6-40 characters and must be same on both field.';
					}
					if($gender!='M' && $gender!='F') 
					{
						$error_flage = 1;
						$error[] = 'Select the gender';
					}
					if(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/',$email)) 
					{
						$error_flag = 1;
						$error[] = 'Enter valid email address.';
					}
					else 
					{
						$domain = preg_replace('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/','',$email);
						if(!checkdnsrr($domain)) 
						{
							$error_flag = 1;
							$error[] = $domain.' is not a valid domain.';
						}
						else if(strlen($email) > MAX_EMAIL_LENGTH)
						{
							$error_flag = 1;
							$error[] = 'Email is too long. Acceptable upto 40 characters .';
						}
					}

					
					//Validate Mobile No.
					if(!preg_match('/^[7-9]\d{9}$/',$phone)) 
					{
						$error_flag = 1;
						$error[] = 'Enter valid 10 digit Mobile Number.';
					}
					
					//Creating query to search for existing input username or email.
					$query = "SELECT * FROM user_info WHERE email='$email'";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data) == 0 && $error_flag == 0) 
					{
						$firstname = strtolower($firstname);
						$lastname = strtolower($lastname);						
						$code = rand(1000000,9999999);
						//$code = 1234567;
						$_SESSION['code'] = $code;
						$_SESSION['signup'] = true;
						$_SESSION['password'] = $password1;
						$_SESSION['first_name'] = $firstname;
						$_SESSION['last_name'] = $lastname;
						$_SESSION['email'] = $email;
						$_SESSION['gender'] = $gender;
						$_SESSION['phone'] = $phone;
						
						//Send code to email address	
						$message = 'Your OTP for Take-Test Make-Test is '.$code.'. Please enter this OTP for verifying your email address.';
						mysqli_close($dbc);
						mail($email, 'TakeTest-MakeTest', $message, 'From: ashutosh@ashutoshindustry.com');
						
						$url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']).'/verification.php';
						header('Location: '.$url);	
					}
					else 
					{	
						$row=mysqli_fetch_array($data);
						
							if($email==$row['email']) 
							{
								$error_flag = 1;
								$error[] = $email.' is already registered try different email.';
							}
					}					
				}
				
			}
			else 
			{
				$error_flag = 1;
				$error[] = 'Captcha Code doesn\'t match.';
			}
			mysqli_close($dbc);
		}
?>
		<div id="formContainer">
		<p>Please fill up the details to Sign Up</p>
		
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
			<fieldset>
				<label for="email">Email: </label>
				<input type="text" name="email" value="<?php if(!empty($email)) echo $email; ?>" /><br />
				<label for="password1">Password: </label>
				<input type="password" name="password1" placeholder="6-40 Characters"/><br />
				<label for="password2">Password (retype):</label>
				<input type="password" name="password2" placeholder="6-40 Characters"/><br />
				<label for="firstname">First Name: </label>
				<input type="text" name="firstname" value="<?php if(!empty($firstname)) echo $firstname; ?>" /><br />
				<label for="lastname">Last Name: </label>
				<input type="text" name="lastname" value="<?php if(!empty($lastname)) echo $lastname; ?>" /><br />
				<label for="gender">Gender: </label>
				<input type="radio" name="gender" value="M" <?php if(!empty($gender) && $gender=='M') echo 'checked="checked"'; ?> />Male
				<input type="radio" name="gender" value="F" <?php if(!empty($gender) && $gender=='F') echo 'checked="checked"'; ?> />Female<br />
				<label for="phone">Mobile No.: </label>
				<input type="text" name="phone" value="<?php if(!empty($phone)) echo $phone; ?>" />
			</fieldset>
			<fieldset>
			<label>Captcha Image: </label>
			<img id="captcha" src="<?php echo ROOT; ?>/captcha.php" alt="Captcha_code" />
			<img id="refresh" src="<?php echo ROOT; ?>/images/refresh_symbol.png" alt="refresh_captcha" height="30px" />
			<!-- <input id="refresh" type="button" name="refresh_button" value="Refresh" /> --><br />
			<label for="captcha_code">Code: </label>
			<input id="captcha_code" type="text" name="captcha_code" />

		</fieldset>
			<input id="submitButton" type="submit" name="submit" value="Sign Up" />
		</form>
		</div>
<?php
		if($error_flag == 1) 
		{
			echo '<ol class="error">';
			foreach($error as $err)
			{
				echo '<li>'.$err.'</li>';
			}
			echo '</ol>';
		}
	}
	else
	{
		echo '<p class="errror">You must Log Out to Sign Up</p>';
	}
	//echo '</div>'; 	
	require_once('include/footer.php');
?>
