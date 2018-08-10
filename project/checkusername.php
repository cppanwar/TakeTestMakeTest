<?php
if(isset($_GET['username'])) 
{
	require_once('include/constants.php');
	require_once('include/connectvars.php');
	$inputdata = strtolower(strip_tags(trim($_GET['username'])));
	
	if(!empty($inputdata)) {
		
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
		$username = mysqli_real_escape_string($dbc, $inputdata);
		if(strlen($username) >= MIN_USERNAME_LENGTH && strlen($username) <= MAX_USERNAME_LENGTH) 
		{
			$query = "SELECT username FROM user_info WHERE username = '$username'";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)!=0) 
			{
				echo '0';
			}
			else 
			{
				echo '1';
			}
			
		}
		mysqli_close($dbc);
	}
}	
?>