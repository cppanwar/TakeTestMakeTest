<?php
	$page_title = 'Bookmarks';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
<script type="text/javascript">
	var root = '<?php echo ROOT; ?>';
</script>
<script type="text/javascript" src="script.js" ></script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	
	if(isLoggedIn()) 
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$userid = $_SESSION['user_id'];
		
		echo '<h3 class="heading">Topics</h3>';
		
		$query = "SELECT user_id, topic_id, topic.name FROM bookmark_topic INNER JOIN topic using(topic_id) WHERE "
		."user_id=$userid";
		$data = mysqli_query($dbc, $query);
		$num = mysqli_num_rows($data);
		if($num != 0) 
		{
			echo '<table class="bookmark_list">';
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="bookmark_node"><a href="'.ROOT.'/taketest/papers.php?topic='.$row['topic_id'].'">'
				.$row['name'].'</a></td><td><button class="2" value="'.$row['topic_id'].'">Remove</button></td></tr>';
			}
			echo '</table>';
		}else
		{
			echo '<p class="error">No bookmarks</p>';
		}
	}
	else
	{
		echo '<p class="error">Please Log-In to access this page</p>';
	}
	require_once('../include/footer.php');
?>