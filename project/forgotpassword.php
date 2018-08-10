<?php
	session_start();
	$page_title = 'Forgot Password';
	$error = '';
	$error_flag = 0;
	require_once('include/constants.php');
	require_once('include/functions.php');
	require_once('include/connectvars.php');
	require_once('include/htmltemplate.php');
?>
	
<?php
	require_once('include/header.php');
	require_once('include/navigation.php');
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$showform1 = 1;
	if(isset($_POST['submit'])) 
	{
		$email = mysqli_real_escape_string($dbc, trim($_POST['email']));
		$email = strtolower($email);
		
		if( empty($email)) 
		{
			$error_flag = 1;
			$error = 'Empty field.';
		}
		else
		{
			$query = "SELECT user_id,username,email FROM user_info WHERE email='$email'";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)==1) 
			{
				$showform1 = 0;
				$showform2 = 1;
				$row = mysqli_fetch_array($data);
				//$code = rand(1000000,9999999);
				$code = 123456;
				$_SESSION['otp'] = $code;
				$_SESSION['temp-user'] = $row['username'];
				$_SESSION['temp-user_id'] = $row['user_id'];
				//$message = 'Your OTP for Password Reset is '.$code.'.'; 
				//mail($row['email'], 'TakeTest-MakeTest', $message);
			}
			else 
			{
				$error_flag = 1;
				$error = 'Username and Email does not match.';
			}
		}
	}
	else if(isset($_POST['verify']) && isset($_SESSION['temp-user'])) 
	{
		$otp = trim($_POST['otp']);
		if($_SESSION['otp'] == $otp) 
		{
			$showform3 = 1;
			$showform2 = 0;
			$showform1 = 0;
			$_SESSION['confirmusername']=$_SESSION['temp-user'];
			$_SESSION['confirmuser_id']=$_SESSION['temp-user_id'];
			$_SESSION['temp-user_id']='';
			$_SESSION['temp-user']='';
		}
		else 
		{
			$showform2 = 1;
			$showform1 = 0;
			$error_flag = 1;
			$error = 'OTP does not match.';
		}
	}
	else if(isset($_POST['newpassword']) && isset($_SESSION['confirmusername'])) 
	{
		$password = mysqli_real_escape_string($dbc, trim($_POST['password']));
		$confirmpassword = mysqli_real_escape_string($dbc, trim($_POST['confirmpassword']));
		
		if(!empty($password)&& !empty($confirmpassword)&&$password == $confirmpassword) 
		{
			if(strlen($password) >= MIN_PASSWORD_LENGTH && strlen($password)<= MAX_PASSWORD_LENGTH )
			{
				$query = "UPDATE user_info SET password=SHA('$password') WHERE username='".$_SESSION['confirmusername']."'";
				if(mysqli_query($dbc, $query)) 
				{
					$_SESSION['user_id'] = $_SESSION['confirmuser_id'];
					$_SESSION['username'] = $_SESSION['confirmusername'];
					$_SESSION['confirmuser_id'] = '';
					$_SESSION['confirmusername'] = '';
					$showform1 = 0;
					$showform2 = 0;
					$showform3 = 0;
					$url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']).'/index.php';
					header('Location: '.$url);
				}
			}
			else 
			{
				$showform1 = 0;
				$showform2 = 0;
				$showform3 = 1;
				$error_flag = 1;
				$error = 'Password must contain 6-40 characters.';
			}
		}
		else 
		{
			$showform1 = 0;
			$showform2 = 0;
			$showform3 = 1;
			$error_flag = 1;
			$error = 'Password must be same in both fields.';
		}
	}
	
	//Output different forms
	if($showform1) 
	{
?>
<div id="formContainer">
	<p>Please enter your email to reset you password</p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
		<fieldset>
			<label>Email: </label>
			<input type="text" name="email" value="<?php if(!empty($email)) echo $email; ?>" />
		</fieldset>
		<input id="submitButton" type="submit" name="submit" value="Submit" />
	</form>
</div>
<?php
	}
	else if($showform2) 
	{
?>
		<div id="formContainer">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
				<fieldset>
					<label>Enter OTP: </label>
					<input type="text" name="otp" />
				</fieldset>
				<input id="submitButton" type="submit" name="verify" value="Verify" />
			</form>
		</div>
<?php
	}
	else if($showform3) 
	{
?>
	<div id="formContainer">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
				<fieldset>
					<label>New Password: </label>
					<input type="password" name="password" /><br />
					<label>Confirm Password: </label>
					<input type="password" name="confirmpassword" />
				</fieldset>
				<input id="submitButton" type="submit" name="newpassword" value="Change Password" />
			</form>
		</div>
<?php	
	}
	if($error_flag) 
	{
		echo '<p class="error">'.$error.'</p>';
	}
	require_once('include/footer.php');
?>