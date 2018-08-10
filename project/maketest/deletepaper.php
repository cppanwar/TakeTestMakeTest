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
			
			$query = "SELECT * FROM paper_info WHERE user_id= $userid AND paper_id=$paperid";
			$data = mysqli_query($dbc, $query);
			
			if(mysqli_num_rows($data) == 1) 
			{
				$row= mysqli_fetch_array($data);
				echo '<h3 class="heading">Delete Paper - '.$row['name'].'</h3>';
				$query = "SELECT * FROM question_paper WHERE paper_id=$paperid ";
				$data = mysqli_query($dbc, $query) or die("Error querying db.");
				$qdeleted = 1;
				$noq = mysqli_num_rows($data);
				if($noq > 0) 
				{
					$qdeleted = 0;
					$row = mysqli_fetch_array($data);
					$query2 = "DELETE FROM question_info WHERE q_id =".$row['q_id'];
					while($row=mysqli_fetch_array($data)) 
					{
						$query2 = $query2." OR q_id=".$row['q_id'];
					}
					$query2 = $query2." LIMIT $noq";
					if (mysqli_query($dbc, $query2))
					{
						$query = "DELETE FROM question_paper WHERE paper_id=$paperid LIMIT $noq";
						if(mysqli_query($dbc, $query))
							$qdeleted = 1;
						else 
						{
							echo '<p class="error">There is some error deleting question paper.</p>';
						}
					}
					else 
					{
						echo mysqli_error($dbc);
						echo '<p class="error">There is some error deleting questions.</p>';
					}
				}
				if($qdeleted) 
				{
					$query = "DELETE FROM paper_info WHERE paper_id=$paperid LIMIT 1";
					if(mysqli_query($dbc, $query))
					{
						echo '<p class="confirm">Paper deleted successfully.</p>';
					}
					else 
					{
						echo '<p class="error">There is some error deleting paper.</p>';
					}
				}
				else 
				{
					echo '<p class="error">There is some error deleting paper part.</p>';
				}				
				
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
			echo '<p class="error">Select Paper to delete. <a href="mypapers.php">Click here</a> to select paper.</p>';
		}
		mysqli_close($dbc);
	}
	else
	{
		echo '<p class="error">Please Log-In to access this page</p>';
	}
	require_once('../include/footer.php');
?>	