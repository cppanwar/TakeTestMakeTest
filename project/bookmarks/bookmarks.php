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
		
		echo '<h3 class="heading">Users</h3>';
		$query = "SELECT * FROM bookmark_user WHERE user_id=$userid";
		$data = mysqli_query($dbc, $query);
		$num = mysqli_num_rows($data);
		if($num != 0) 
		{
			$i=1;
			echo '<table class="bookmark_list">';
			while($i<=5 && $row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="bookmark_node"><a href="'.ROOT.'/profile/viewprofile.php?user='.$row['b_user'].'">'
				.$row['b_user'].'</a></td><td><button class="4" value="'.$row['b_user'].'">Remove</button></td></tr>';
				$i++;
			}
			echo '</table>';
			if($num>5) 
			echo '<a class="seeMore" href="users.php">See more &raquo;</a>';
		}else
		{
			echo '<p class="error">No bookmarks</p>';
		}
		
		echo '<h3 class="heading">Papers</h3>';
		
		$query = "SELECT bp.user_id, bp.paper_id,pi.name,topic.name AS tname FROM bookmark_paper AS bp INNER JOIN ".
		"paper_info AS pi using(paper_id) INNER JOIN topic using(topic_id) WHERE bp.user_id=$userid";
		$data = mysqli_query($dbc, $query);
		$num = mysqli_num_rows($data);
		if($num != 0) 
		{
			$i=1;
			echo '<table class="bookmark_list">';
			while($i<=5 && $row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="bookmark_node"><a href="'.ROOT.'/taketest/instructions.php?paper='.$row['paper_id'].'">'
				.$row['name'].'</a></td><td>'.$row['tname'].'</td><td><button class="3" value="'.$row['paper_id'].'">Remove</button></td></tr>';
				$i++;
			}
			echo '</table>';
			if($num>5) 
			echo '<a class="seeMore" href="papers.php">See more &raquo;</a>';
		}else
		{
			echo '<p class="error">No bookmarks</p>';
		}
		
		echo '<h3 class="heading">Topics</h3>';
		
		$query = "SELECT user_id, topic_id, topic.name FROM bookmark_topic INNER JOIN topic using(topic_id) WHERE "
		."user_id=$userid";
		$data = mysqli_query($dbc, $query);
		$num = mysqli_num_rows($data);
		if($num != 0) 
		{
			$i=1;
			echo '<table class="bookmark_list">';
			while($i<=5 && $row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="bookmark_node"><a href="'.ROOT.'/taketest/papers.php?topic='.$row['topic_id'].'">'
				.$row['name'].'</a></td><td><button class="2" value="'.$row['topic_id'].'">Remove</button></td></tr>';
				$i++;
			}
			echo '</table>';
			if($num>5) 
			echo '<a class="seeMore" href="topics.php">See more &raquo;</a>';
		}else
		{
			echo '<p class="error">No bookmarks</p>';
		}
		
		echo '<h3 class="heading">Categories</h3>';
		
		$query = "SELECT user_id, category_id, category.name FROM bookmark_category INNER JOIN category "
		."using(category_id) WHERE user_id=$userid";
		$data = mysqli_query($dbc, $query);
		$num = mysqli_num_rows($data);
		if($num != 0) 
		{
			$i=1;
			echo '<table class="bookmark_list">';
			while($i<=5 && $row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="bookmark_node"><a href="'.ROOT.'/taketest/topic.php?category='.$row['category_id'].'">'
				.$row['name'].'</a></td><td><button class="1" value="'.$row['category_id'].'">Remove</button></td></tr>';
				$i++;
			}
			echo '</table>';
			if($num>5) 
			echo '<a class="seeMore" href="categories.php">See more &raquo;</a>';
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