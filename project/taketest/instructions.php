<?php
	$page_title = 'Take Test';
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
		echo '<p class="error">You must log in to take test. If you don\'t have an account please sign up.</p>';
		require_once('../include/footer.php');
		exit();
	}
	if(isset($_GET['paper']))
	{
		//Connecting to database
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
		$query = "SELECT noq FROM paper_info WHERE paper_id=".$_GET['paper'];
		
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data)==0) 
		{
			echo '<p class="error">No such paper exist.</p>';
			
		}
		else
		{
?>
<h4 class="heading">Instructions</h4>
<ul>
	<li>Each question carries 1 mark.</li>
	<li>Questions not attempted shall be ignored.</li>
	<li>For every wrong answer <sup>1</sup>/<sub>4</sub> mark will be deducted.</li>
</ul>
<form method="post" action="test.php" >
<input type="hidden" name="paper" value="<?php echo $_GET['paper'] ?>" />
<input type="submit" value="Take Test" name="submit" />
</form>
<?php
		}
		mysqli_close($dbc);
	}
	else 
	{
		echo '<p class="error">Select the topic first to participate in test.</p>';
		require_once('../include/footer.php');
		exit();
	}
	require_once('../include/footer.php');
?>