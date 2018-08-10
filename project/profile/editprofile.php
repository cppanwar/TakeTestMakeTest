<?php
	$page_title = 'My Profile';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');

	
	if(!isLoggedIn()) 
	{
		echo '<p class="error">You must log in to access this page.</p>';
		require_once('../include/footer.php');
		exit();
	}
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$error = array();
	$error_flag = 0;
	if(isset($_POST['editprofile'])) 
	{
		$firstname = mysqli_real_escape_string($dbc, strtolower( strip_tags(trim($_POST['firstname']))));
		$lastname  = mysqli_real_escape_string($dbc, strtolower( strip_tags(trim($_POST['lastname']))));
		if(isset($_POST['gender'])) 
		$gender    = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['gender'])));
		 		
		$mobile	  = mysqli_real_escape_string($dbc, strip_tags(trim($_POST['mobile'])));
		
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
		if(empty($mobile)) 
		{
			$error_flag = 1;
			$error[] = 'Enter your Mobile Number.';
		}
		else if(!preg_match('/^[7-9]\d{9}$/',$mobile)) 
		{
				$error_flag = 1;
				$error[] = 'Enter valid 10 digit Mobile Number.';
		}
		if($error_flag==0) 
		{
			$query = "UPDATE user_info SET first_name='$firstname' WHERE user_id=".$_SESSION['user_id'];
			mysqli_query($dbc , $query);
			$query = "UPDATE user_info SET last_name='$lastname' WHERE user_id=".$_SESSION['user_id'];
			mysqli_query($dbc , $query);
			$query = "UPDATE user_info SET gender='$gender' WHERE user_id=".$_SESSION['user_id'];
			mysqli_query($dbc , $query);
			$query = "UPDATE user_info SET mobile='$mobile' WHERE user_id=".$_SESSION['user_id'];
			mysqli_query($dbc , $query);
			
			$url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']).'/viewprofile.php';
			header('Location: '.$url);
		}
	}
		$query = "SELECT * FROM user_info WHERE user_id=".$_SESSION['user_id'];
		$data = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($data);
?>
	<div id="formContainer" >
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<fieldset>
			<label>First Name: </label>
			<input type="text" name="firstname" value="<?php echo $row['first_name']; ?>" /><br />
			<label>Last Name: </label>
			<input type="text" name="lastname" value="<?php echo $row['last_name']; ?>" /><br />
			<label>Gender: </label>
			<input type="radio" name="gender" value="M" <?php if($row['gender']=='M') echo 'checked="checked"'; ?> />Male 
			<input type="radio" name="gender" value="F" <?php if($row['gender']=='F') echo 'checked="checked"'; ?> />Female <br />
			<label>Mobile: </label>
			<input type="text" name="mobile" value="<?php echo $row['mobile']; ?>" />
		</fieldset>
		<input id="submitButton" type="submit" value="Save" name="editprofile" />
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
	require_once('../include/footer.php');
?>