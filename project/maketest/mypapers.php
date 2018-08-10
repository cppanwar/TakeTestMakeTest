<?php
	$page_title = 'Make Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
	<script type="text/javascript">
		function sharepaper () 
		{
			if(confirm("You can't edit paper once it's published. Are you sure you want to publish this paper."))
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
		function deletepaper ()
		{
			if (confirm("Are you sure you want to delete this paper."))
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
		window.onload = function () {
			var i;
			var share = document.getElementsByClassName("share");
			for (i=0 ; i<share.length; i++){ 
				share[i].onclick = sharepaper;
			}
			var deletenode = document.getElementsByClassName("delete");
			for (i=0 ; i< deletenode.length; i++)
			{ 
				deletenode[i].onclick = deletepaper;
			}  
		};
	</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	
	if(isLoggedIn()) 
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if(isset($_POST['submit'])) 
		{
			if(!empty($_POST['topic']) && !empty($_POST['papername'])) 
			{
				$userid = $_SESSION['user_id'];
				$topicid = $_POST['topic'];
				$papername = $_POST['papername'];
				$query = "INSERT INTO paper_info(user_id, topic_id, date, name) VALUES ($userid ,$topicid ,".
				" NOW(), '$papername' )";
				
				mysqli_query($dbc, $query)
				or die('<p class="error">There is some error creating Paper.</p>');
				$url = 'http://' .$_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']).'/mypapers.php';
				header('Location: '.$url);
			}
			else
			{
				echo '<p class="error">Select the Topic and Enter Paper Name properly</p>';
			}			
		}
	if(isset($_GET['paper'])) 
		{
			//---------------------------Input data-------------------------
			$userid = $_SESSION['user_id'];
			$paperid = mysqli_real_escape_string($dbc, trim($_GET['paper']));
			
			$query = "SELECT * FROM paper_info WHERE user_id= $userid AND paper_id=$paperid AND published='N'";
			$data = mysqli_query($dbc, $query);
			
			if(mysqli_num_rows($data) == 1) 
			{
				$query = "UPDATE paper_info SET published='Y' WHERE paper_id=$paperid";
				mysqli_query($dbc, $query);
				
				$query = "UPDATE paper_info SET date=NOW() WHERE paper_id=$paperid";
				mysqli_query($dbc, $query);
			}
		}
		
		$query = "SELECT topic.name AS tname, ppr.name AS pname, ppr.paper_id, ppr.date, ppr.hits, ppr.rating FROM paper_info AS ".
		"ppr INNER JOIN topic using(topic_id) WHERE user_id =".$_SESSION['user_id']." AND published='Y' ORDER BY ppr.date DESC "	;
		$data = mysqli_query($dbc, $query);
		echo '<h3 class="heading">Published Papers</h3>';
		echo '<table id="paper_list">';
		echo '<tr id="paper_list_header"><th>Topic</th><th>Paper Name</th><th>Date</th><th>Hits</th>'.
		'<th>Rating</th><th>Options</th></tr>';
		if(mysqli_num_rows($data)!=0 ) 
		{
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td>'.$row['tname'].'</td>';
				echo '<td>'.$row['pname'].'</td>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td>'.$row['hits'].'</td>';
				echo '<td>'.$row['rating'].'</td>';
				echo '<td><a class="icon" href="viewpaper.php?paper='.$row['paper_id'].'" title="View Paper"><img src="../images/view.png" /></a> '.
				'<a class="icon" href="deletepaper.php?paper='.$row['paper_id'].'" title="Delete Paper"><img class="delete" src="../images/delete.png" /></a></td></td></tr>';
			}
		}
		else
		{
			echo '<tr><td style="text-align: center;" colspan="6" >No Papers</td></tr>';
		}
		echo '</table><br />';
		
		echo '<h3 class="heading">Saved Papers</h3>';
		
		$query = "SELECT topic.name AS tname, ppr.name AS pname, ppr.paper_id, ppr.date, ppr.noq FROM paper_info AS ".
		"ppr INNER JOIN topic using(topic_id) WHERE user_id =".$_SESSION['user_id']." AND published='N' ORDER BY ppr.date DESC "	;
		
		$data = mysqli_query($dbc, $query);
		
		echo '<table id="paper_list">';
		echo '<tr id="paper_list_header"><th>Topic</th><th>Paper Name</th><th>Date</th><th>NOQ</th><th>Options</th></tr>';
		if(mysqli_num_rows($data)!=0 ) 
		{
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<tr><td>'.$row['tname'].'</td>';
				echo '<td>'.$row['pname'].'</td>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td>'.$row['noq'].'</td>';
				echo '<td><a class="icon" href="viewpaper.php?paper='.$row['paper_id'].'" title="View Paper"><img src="../images/view.png" /></a> '.
				'<a class="icon" href="editpaper.php?paper='.$row['paper_id'].'" title="Edit Paper"><img src="../images/edit.png" /></a> '.
				' <a class="icon" href="deletepaper.php?paper='.$row['paper_id'].'" title="Delete Paper"><img class="delete" src="../images/delete.png" /></a> '.
				' <a class="icon" href="mypapers.php?paper='.$row['paper_id'].'" title="Publish Paper"><img class="share" src="../images/share.png" /></a></td></tr>';
			}
		}
		else
		{
			echo '<tr><td style="text-align: center;" colspan="6" >No Papers</td></tr>';
		}
		echo '</table><br />';
		
		echo '<h3 class="heading">Create Paper</h3>';
		
		$query = "SELECT * FROM topic ORDER BY name";
		$data = mysqli_query($dbc, $query);
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		echo '<fieldset>';
		echo '<label for="topic" >Select Topic: </label>';
		echo '<select name="topic">';
		echo '<option value="" disabled selected>--------Select Topic--------</option>';
		while($row=mysqli_fetch_array($data)) 
		{
			echo '<option value="'.$row['topic_id'].'">'.$row['name'].'</option>';
		}	
		echo '</select><br />';
		echo '<label for="papername">Paper Name: </label>';
		echo '<input name="papername" type="text" />';
		echo '</fieldset>';
		echo '<input name="submit" value="Create Paper" type="submit" />';
		echo '</form>';
	}
	else
	{
		echo '<p class="error">Please Log-In to access this page</p>';
	}
	require_once('../include/footer.php');
?>
