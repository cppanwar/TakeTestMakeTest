<?php
	session_start();

		$_SESSION[] = array();
		
		if(isset($_COOKIE[session_name()])) 
		{
			setcookie(session_name(),'',time()-3600);
		}
		session_destroy();
	
	setcookie('user_id', '', time()-3600);
	setcookie('username', '', time()-3600);
  // Redirect to the Log In page
  $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php';
  header('Location: ' . $home_url);
?>