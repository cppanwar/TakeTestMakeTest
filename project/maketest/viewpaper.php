<?php
	$page_title = 'Make Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	
	if(isLoggedIn()) 
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$userid = $_SESSION['user_id'];
		if(isset($_GET['paper'])) 
		{
			//---------------------------Input data-------------------------
			$paperid = mysqli_real_escape_string($dbc, trim($_GET['paper']));
			$query = "SELECT * FROM paper_info WHERE user_id= $userid AND paper_id=$paperid ";
			$data = mysqli_query($dbc, $query);
			
			if(mysqli_num_rows($data) == 1) 
			{	
				$row = mysqli_fetch_array($data);
				echo '<h3 class="heading">View Paper - '.$row['name'].'</h3>';
				$query = "select question_info.question, question_info.option_a, question_info.option_b, question_info.option_c, ".
				"question_info.option_d,question_info.answer from question_info inner join question_paper ".
				" using(q_id) where question_paper.paper_id=$paperid";
				
				$data = mysqli_query($dbc, $query) or die('<p class="error">There is some problem loading paper.</p>');
				echo '<ol id="testpaper" >';
				while($row=mysqli_fetch_array($data)) 
				{
					echo '<div class="questionSet">';
					echo '<li class="question">'.$row['question'].'</li>';
					echo '<ol>';
					echo '<li class="option">'.$row['option_a'].'</li>';
					echo '<li class="option">'.$row['option_b'].'</li>';
					echo '<li class="option">'.$row['option_c'].'</li>';
					echo '<li class="option">'.$row['option_d'].'</li>';
					echo '<li style="list-style-type: none; background-color: #99ff66;" class="option">'.$row['answer'].'</li>';
					echo '</ol></div>';
				}				
				echo '</ol>';
			}
			else 
			{
				echo '<p class="error">Paper does not exist.</p>';
				require_once('../include/footer.php');
				mysqli_close($dbc);
				exit();
			}
		}
		else 
		{
			echo '<p class="error">Select Paper to edit paper. <a href="mypapers.php">Click here</a> to select paper.</p>';
		}
		mysqli_close($dbc);
	}
	else
	{
		echo '<p class="error">Please Log-In to access this page</p>';
	}
	require_once('../include/footer.php');
?>