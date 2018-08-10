<?php
	$page_title ='Log In';
	$error = array();
	require_once('include/constants.php');
	require_once('include/sessionstart.php');
	require_once('include/functions.php');
	require_once('include/connectvars.php');
	require_once('include/htmltemplate.php');
	?>
	<script type="text/javascript">
		var usernameRequest;
 //Username check
 	window.onload = function () {
				document.getElementById("checkUsername").onclick=function () {
					if (window.XMLHttpRequest) 
					{ 
    					usernameRequest = new XMLHttpRequest();
					} 
					else if (window.ActiveXObject) 
							{ 
    								usernameRequest = new ActiveXObject("Microsoft.XMLHTTP");
							}
					usernameRequest.onreadystatechange = function () {
						if (usernameRequest.readyState==4 && usernameRequest.status == 200) {
							var output = document.getElementById("usernameResults");
							if(usernameRequest.responseText.substring(0,1)== "1" )
							{
								output.innerHTML = "Username is available";
								output.style.color = "#31B520";
								output.style.fontSize = "20px";
								
							} else if(usernameRequest.responseText.substr(0,1)== "0") {
								
								output.innerHTML = "Username is not available";
								output.style.color = "#ED2D38";
								output.style.fontSize = "20px";
								
							}
							else {
								output.innerHTML = '';
							}
							
						}
					}
					var username = document.getElementById("username").value;
					usernameRequest.open('GET','checkusername.php?username='+username,true);
					usernameRequest.send();
			};
		};
	</script>
	<?php
	require_once('include/header.php');
	require_once('include/navigation.php');
	//echo '<div	id="content">';
	
	if(!isLoggedIn())
	{
		$error_flag =0;
		if(isset($_POST['submit'])) 
		{
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
		 	$user_email = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['email'])));
			$user_password = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['password'])));
			
			if(true/*captchaCheck($_POST['captcha_code'])*/) 
			{
	
		 		//Following IF statements check for empty fields.
		 		if(empty($user_email)) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Email field is empty.';
		 		}
		 				

		 		if(empty($user_password)) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Password field is empty.';
		 		}
				if(strlen($user_password) < MIN_PASSWORD_LENGTH) 
				{
					$error_flag = 1;
					$error[] = 'Password must contain atleast 6 characters.';
				}
				if($error_flag == 0) 
				{
					//Look up the email and password in the database
					$query = "SELECT user_id, username, email FROM user_info WHERE email = '$user_email' ".
					"AND password = SHA('$user_password')";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data) == 1) 
					{
						$row = mysqli_fetch_array($data);
						//Check Whether user has confirmed its email or not
						if($row['username']!='NONE')
						{
							mysqli_close($dbc);
						//User is verified continue Surfing.
						//Setting Session Vars
						$_SESSION['user_id'] = $row['user_id'];
						$_SESSION['username'] = $row['username'];
					
						//Setting cookies
						setcookie('user_id', $row['user_id'], time()+(60*60*24*30));
						setcookie('username', $row['username'], time()+(60*60*24*30));
						
						$home_url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). '/index.php';
						//Redirecting to the Homepage
						header('Location: '.$home_url);
						}
						else 
						{
							//User has not verified its email redirect it to verification page.
							$_SESSION['NONE'] = $row['user_id'];
						}
					}
					else 
					{
						//Entered Username/Password doesn't exist in Database
						$error_flag = 1;
						$error[] = 'Sorry, you must enter a valid Email and password to log in.';
					}
				}				
			}
			else 
			{
				$error_flag = 1;
				$error[]='Captcha doesn\'t match';
			}
			
			mysqli_close($dbc);
			
		}else if(isset($_POST['setusername'])) 
		{
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$username  = mysqli_real_escape_string($dbc, strtolower(strip_tags(trim($_POST['username']))));
			$username = strtolower($username);
			if(empty($username)) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Username field is empty.';
		 		}
		 	else if(!preg_match('/^[a-zA-Z0-9\._]*$/', $username)) 
		 		{
		 			$error_flag = 1;
		 			$error[] = 'Username can have alphabets, numbers, dot and underscore only.';
		 		}
		 	else if(strlen($username) < MIN_USERNAME_LENGTH || strlen($username) > MAX_USERNAME_LENGTH ) 
					{
						$error_flag = 1;
						$error[] = 'Username must contain  6-40 characters.';
					}
			else
			{
				$query = "SELECT username FROM user_info WHERE username='$username'";
				$data =  mysqli_query($dbc, $query);
				if(mysqli_num_rows($data) > 0 )
				{
						$error_flag = 1;
						$error[] = $username.' is already registered try different username.';
				}
			}
			
			if($error_flag == 0) 
			{
				$query = "UPDATE user_info SET username='$username' WHERE user_id=".$_SESSION['NONE'];
				if(mysqli_query($dbc, $query))
				{					
					$_SESSION['user_id'] = $_SESSION['NONE'];
					$_SESSION['username'] = $username;
					
					setcookie('user_id', $_SESSION['NONE'], time()+(60*60*24*30));
					setcookie('username', $username, time()+(60*60*24*30));
					
					$_SESSION['NONE'] = '';
					$home_url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). '/index.php';
					//Redirecting to the Homepage
					header('Location: '.$home_url);
				}
			}
		}
		
		
		if(isset($_SESSION['NONE'])&&!empty($_SESSION['NONE'])) 
		{
?>
	<div id="loginCard">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
			<fieldset>
				<label for="username">Set Your Username: </label>
				<input id="username" type="text" name="username" value="<?php if(!empty($username)) echo $username; ?>" placeholder="6-40 Characters" />
				<input type="button" id="checkUsername" value="Check availability" /><div style="text-align: center;"><span id="usernameResults"></span></div><br />
			</fieldset>
			<input type="submit" name="setusername" value="Submit" />
			</form>
			<div style="width: 90%; text-align: right;"><a style="text-decoration: none; color: blue; font-size: 17px;" href="logout.php">Log In again?</a></div>
	</div>
<?php	
		}
		else{
?>
<div id="loginCard">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
		<fieldset>
			<label for="email">Email</label>
			<input type="text" name="email" placeholder="Enter your Email" value="<?php if(!empty($user_email)) echo $user_email; ?>" />
			<br />
			<label for="password" >Password</label>
			<input type="password" name="password" />
		</fieldset>
		<fieldset style="display: none;">
			<legend>Captcha</legend>
			<label>Captcha Image: </label>
			<img id="captcha" src="<?php echo ROOT; ?>/captcha.php" alt="Captcha_code" />
			<img id="refresh" src="<?php echo ROOT; ?>/images/refresh_symbol.png" alt="refresh_captcha" height="30px" />
			<!-- <input id="refresh" type="button" name="refresh_button" value="Refresh" /> --><br />
			<label for="captcha_code">Code: </label>
			<input id="captcha_code" type="text" name="captcha_code" />
		</fieldset>
		<input id="loginButton" type="submit" name="submit" value="Log In" />
	</form>
	<div style="width: 90%; text-align: right;"><a style="text-decoration: none; color: blue; font-size: 17px;" href="forgotpassword.php">Forgot Password?</a></div>
</div>
<?php
	}
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
		echo '<p class="errror">You must Log Out to Sign In</p>';
	}
	require_once('include/footer.php');
?>