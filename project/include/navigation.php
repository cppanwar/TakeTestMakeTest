<nav>
	<?php 
		if(isset($_SESSION['user_id'])) 
		{
			if($page_title=='Take Test') 
			{
		    	echo '<a href="'.ROOT.'/taketest/category.php" id="current_page">Take Test</a> ';
		   }
		   else 
		   {
		   	echo '<a href="'.ROOT.'/taketest/category.php">Take Test</a> ';
		   }
		   if($page_title=='My Profile') 
		   {
    		 echo '<a href="'.ROOT.'/profile/viewprofile.php" id="current_page">My Profile</a> ';
    		}
    		else 
    		{
    			echo '<a href="'.ROOT.'/profile/viewprofile.php">My Profile</a> ';
    		}
    		if($page_title=='Make Test') 
    		{
    		 	echo '<a href="'.ROOT.'/maketest/mypapers.php" id="current_page">Make Test</a> ';
    		}
    		else 
    		{
    			echo '<a href="'.ROOT.'/maketest/mypapers.php">Make Test</a> ';
    		}
    		if($page_title=='Bookmarks') 
    		{
    		 	echo '<a href="'.ROOT.'/bookmarks/bookmarks.php" id="current_page">Bookmarks</a>';
    		}
    		else 
    		{
    			echo '<a href="'.ROOT.'/bookmarks/bookmarks.php">Bookmarks </a>';
    		}
		}
		else 
		{
			if($page_title=='Log In') 
			{
				echo '<a id="current_page">Log In</a> ';
			}
			else 
			{
				echo '<a href="'.ROOT.'/login.php">Log In</a> ';
			}
			if($page_title=='Sign Up') 
			{
				echo '<a id="current_page">Sign Up</a>';
			}
			else 
			{
				echo '<a href="'.ROOT.'/signup.php">Sign Up</a>';
			}
		}
	?>
</nav>
<section>