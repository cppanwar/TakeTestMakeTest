<?php
	$page_title = 'Take Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
	?>
	<style type="text/css">
		.pagelink{
			display: inline-block;
			width: 300px;
			background-color: #a7a7a7;
			text-decoration: none;
			padding: 10px;
			color:black;
			border-radius: 5px;
			box-shadow: 0px 2px 2px rgb(0,0,0,0.3);
			
		}
	</style>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	if(isLoggedIn()) 
	{
		if(isset($_POST['submit']) && isset($_SESSION['paperid']) && $_SESSION['paperid']!= 0) 
		{
			
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			
			echo '<h3 class="heading">Thank you</h3>';
			echo '<p><a class="pagelink" href="category.php">Go back Category page &raquo;</a></p>';
			
			$query = "SELECT * FROM user_test WHERE paper_id=".$_SESSION['paperid']." AND user_id=".$_SESSION['user_id'];
			$data = mysqli_query($dbc, $query);
			$firsttime = 0;
			if(mysqli_num_rows($data)==0) 
				$firsttime = 1; 
			
			if($firsttime)
			{
			$query = "SELECT hits, rating FROM paper_info WHERE paper_id=".$_SESSION['paperid'];
			$data = mysqli_query($dbc, $query);
			$row = mysqli_fetch_array($data);
			
			$userrating = $_POST['rating'];
			$hits = $row['hits'];
			$oldrating = $row['rating'];
			$newrating = (($oldrating * $hits)+ $userrating)/ ($hits+1);
			$hits++;
			
			$query = "UPDATE paper_info SET hits=$hits WHERE paper_id=".$_SESSION['paperid'];
			mysqli_query($dbc, $query);
			$query = "UPDATE paper_info SET rating=$newrating WHERE paper_id=".$_SESSION['paperid'];
			mysqli_query($dbc, $query);
			}
			$user = $_SESSION['user_id'];
			$paperid = $_SESSION['paperid'];
			$correct = $_SESSION['correct'];
			$wrong = $_SESSION['wrong'];
			$marks = $_SESSION['marks'];
			$_SESSION['paperid'] = 0; 
			$_SESSION['correct'] = 0; 
			$_SESSION['wrong'] = 0; 
			$_SESSION['marks'] = 0;
			$query = "INSERT INTO user_test(user_id, paper_id, date_time, correct, wrong, marks) ".
			"VALUES ($user ,$paperid , NOW() ,$correct ,$wrong ,$marks )";
			mysqli_query($dbc, $query);
			
			mysqli_close($dbc);
		}
		else
		{
			echo '<p class="error">This page is shown after the test. You didn\'t take the test.</p>';
		}
	}
	else
	{
		echo '<p class="error">You must log in to take test. If you don\'t have an account please sign up.</p>';
	}
	require_once('../include/footer.php');
?>