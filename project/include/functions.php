<?php
	//This function checks whether the user is logged in or not.
	//If user is logged in it returns true otherwise returns false.	
	function isLoggedIn () 
	{	
		if(isset($_SESSION['user_id']) &&isset( $_SESSION['username'])) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	//This function checks whether the parameter is valid captcha or not.
	
	function captchaCheck ($captcha_code)
	{
		if($captcha_code == $_SESSION['captcha_code']) 
		{
			$_SESSION['captcha_code'] = rand(100000000, 1000000000);
			return true;
		}
		else 
		{
			$_SESSION['captcha_code'] = rand(100000000, 1000000000);
			return false;
		}
	}
	
	function addCss ($css_file) 
	{
		echo '<link rel="stylesheet" type="text/css" href="<?php echo ROOT; ?>'.$css_file.'"  />';
	}
	
	function addScript ($script_file)
	{
		echo '<script type="text/javascript" src="'.$script_file.'" ></script>'; 
	}
	//Checks Whether Date is valid or not.
	function isValidDate ($date_string) 
	{
		// DD/MM/YYYY
		$date = explode('/',$date_string);
		$day = intval($date[0]);
		$month = intval($date[1]);
		$year = intval($date[2]);
		if(checkdate($month, $day, $year))
		{
			//echo $date_string.' is valid<br />';
			return true;
		}
		else 
		{
			//echo $date_string.' is not valid<br />';
			return false;
		}
	}
	
	
	//isValidDate('12/12/1234');
	//isValidDate('1/1/1');
	//isValidDate('29/02/2015');
?>