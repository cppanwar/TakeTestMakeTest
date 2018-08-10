<?php
	session_start();
	$page_title = 'Verfiy Email';
	$error = '';
	require_once('include/constants.php');
	require_once('include/functions.php');
	require_once('include/connectvars.php');
	require_once('include/htmltemplate.php');
?>
<script type="text/javascript">
	var root = '<?php echo ROOT; ?>';
	window.onload = function () {
				//Captcha code
			 refresh = document.getElementById("refresh");
			 refresh.onclick=refreshCaptcha;
		};
		
		function refreshCaptcha () {
			document.getElementById('captcha').src = root+'/captcha.php?' + Math.random();
  			document.getElementById('captcha_code').value = '';
		}
</script>
<?php
	require_once('include/header.php');
	require_once('include/navigation.php');
	//echo '<div	id="content">';
	$url = 'http://'. $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) .'/index.php';
	if( $_SESSION['signup']== true && !isLoggedIn()) 
	{	
		$error_flag = 0;
		if(isset($_POST['verifymail'])) 
		{
			if(captchaCheck($_POST['captcha_code'])) 
			{
				//$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				//$query = "SELECT * FROM user_verification WHERE user_id=".$_SESSION['verify'];
				//$data = mysqli_query($dbc , $query);
				//$row = mysqli_fetch_array($data);
				if($_SESSION['code'] == $_POST['otp']) 
				{
					$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					$password = $_SESSION['password'];
					$firstname = $_SESSION['first_name'];
					$lastname = $_SESSION['last_name'];
					$email = $_SESSION['email'];
					$gender = $_SESSION['gender'];
					$phone = $_SESSION['phone'];
					$_SESSION['signup'] = false;
					$query = "INSERT INTO user_info(username, password, first_name, email, last_name, gender, mobile) ".
						"VALUES('NONE', SHA('$password'), '$firstname', '$email', '$lastname', '$gender', '$phone' )";
					mysqli_query($dbc, $query);
					
					echo '<p class="confirm">Your account has been created successfully. Please <a href="login.php">Log In</a>.</p>';
					require_once('include/footer.php');
					mysqli_close($dbc);
					exit();
					
				}
				else
				{
					$error_flag = 1;
					$error = 'OTP doesn\'t match.';
				}
			}
			else 
			{
				$error_flag = 1;
				$error ='Captcha doesn\'t match.';
			}
		}
		/*if(isset($_GET['sendmail']))
		{
			
		}*/
		//Load the form
		
?>
	<div id="formContainer" >
		<p>You have to verfiy your email. A mail has been sent to your email with OTP. Check
		 in your spam folder if not in inbox.</p>
		 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		 	<fieldset>
		 		<legend>Verify Email</legend>
		 		<label for="otp">Enter OTP :</label>
		 		<input type="text" name="otp" /><br />
		 		<!-- <a href="<?php echo $_SERVER['PHP_SELF']; ?>?sendmail=1">Send mail again</a> -->
		 	</fieldset>
		 	<fieldset>
			<legend>Captcha</legend>
			<label>Captcha Image: </label>
			<img id="captcha" src="<?php echo ROOT; ?>/captcha.php" alt="Captcha_code" />
			<img id="refresh" src="<?php echo ROOT; ?>/images/refresh_symbol.png" alt="refresh_captcha" height="30px" /><br />
			<label for="captcha_code">Code: </label>
			<input id="captcha_code" type="text" name="captcha_code" />
		</fieldset>
		 	<input id="submitButton" type="submit" name="verifymail" value="Verify" />
		 </form>
		 </div>
<?php
		
		if($error_flag==1)
		{
			echo '<p class="error">'.$error.'</p>';
		}
		require_once('include/footer.php');
	}
	else
	{
		header('Location: '.$url);
	}
?>