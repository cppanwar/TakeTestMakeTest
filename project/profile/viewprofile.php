<?php
	$page_title = 'My Profile';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
<script type="text/javascript">
	window.onload = function () {
		if(document.getElementById("abookmark"))
			{
				document.getElementById("abookmark").onclick = addBookmark;
			} else if (document.getElementById("rbookmark")) {
				document.getElementById("rbookmark").onclick = removeBookmark;				
			}
	};
	function addBookmark (eventObj) {
					var bookmark = eventObj.target;
					var username = bookmark.getAttribute("value");
					
					if (window.XMLHttpRequest) 
					{ 
    					bookmarkRequest = new XMLHttpRequest();
					} 
					else if (window.ActiveXObject) 
							{ 
    								bookmarkRequest = new ActiveXObject("Microsoft.XMLHTTP");
							}
					bookmarkRequest.onreadystatechange = function () {
						if (bookmarkRequest.readyState == 4 && bookmarkRequest.status == 200)
						{
							if (bookmarkRequest.responseText.substring(0,1)== "1" )
							{
								var btn = document.getElementById("abookmark");
								btn.onclick = removeBookmark;
								btn.setAttribute("id","rbookmark");
								btn.innerHTML="Remove from Bookmarks";
								btn.style.backgroundColor="#D40000";
								btn.style.color="white";
								
							}
						}
					};
					var root = '<?php echo ROOT; ?>';
					bookmarkRequest.open('GET',root+'/bookmarks/addbookmark.php?type=4&bid='+username,true);
					bookmarkRequest.send();					
				}
			function removeBookmark (eventObj) {
					var bookmark = eventObj.target;
					var username = bookmark.getAttribute("value");
					
					if (window.XMLHttpRequest) 
					{ 
    					bookmarkRequest = new XMLHttpRequest();
					} 
					else if (window.ActiveXObject) 
							{ 
    								bookmarkRequest = new ActiveXObject("Microsoft.XMLHTTP");
							}
					bookmarkRequest.onreadystatechange = function () {
						if (bookmarkRequest.readyState == 4 && bookmarkRequest.status == 200)
						{
							if (bookmarkRequest.responseText.substring(0,1)== "1" )
							{
								var btn = document.getElementById("rbookmark");
								btn.onclick=addBookmark;
								btn.setAttribute("id","abookmark");
								btn.innerHTML="Add to Bookmarks";
								btn.style.backgroundColor="green";
								btn.style.color="white";
								
							}
						}
					};
					var root = '<?php echo ROOT; ?>';
					bookmarkRequest.open('GET',root+'/bookmarks/removebookmark.php?type=4&bid='+username,true);
					bookmarkRequest.send();					
				};
</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');

	
	if(!isLoggedIn()) 
	{
		echo '<p class="error">You must log in to access this page.</p>';
		require_once('../include/footer.php');
		exit();
	}
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if(isset($_GET['user'])) 
	{
		$username = mysqli_real_escape_string($dbc, trim($_GET['user']));
		
		$query = "SELECT username, first_name, last_name, gender FROM user_info WHERE username='$username'";
		
		$data = mysqli_query($dbc, $query) or die('<p class="error">There is seems to be a problem.</p>');
		if(mysqli_num_rows($data)==1)
		{
		$row = mysqli_fetch_array($data);
		echo '<h3 class="heading">'.$username.' Profile</h3>';
		if(isLoggedIn()) 
		{
			
			$userid = $_SESSION['user_id'];
			$user = mysqli_real_escape_string($dbc, trim($_GET['user']));
			
			$query = "SELECT * FROM bookmark_user WHERE user_id=$userid AND b_user='$user'";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)!=1)
			{
				echo '<div style="float: left;"><button id="abookmark" value="'.$user.'">Add to Bookmarks</button></div>';
			}
			else 
			{
				echo '<div style="float: left;"><button  id="rbookmark" value="'.$user.'">Remove from Bookmarks</button></div>';
			}
		}
		echo '<table style="clear: both;" id="info">';
		echo '<tr><td class="field">Username</td><td class="value">'.$row['username'].'</td></tr>';
		echo '<tr><td class="field">Name</td><td class="value">'.$row['first_name'].' '.$row['last_name'].'</td></tr>';
		echo '<tr><td class="field">Gender</td><td class="value">';
		if($row['gender']=='M')
			echo 'Male';
		else 
			echo 'Female';
		echo '</td></tr>';
		echo '</table>';
		
		echo '<h3 class="heading">Popular Tests Published By '.$username.'</h3>';
		
		$query = "SELECT topic.name AS tname, ppr.hits ,ppr.rating, ppr.noq, ppr.date, ppr.name FROM paper_info ".
		"AS ppr inner join user_info using(user_id) INNER JOIN topic using(topic_id) where username='$username' ".
		"AND published='Y' ORDER BY ppr.hits DESC LIMIT 10";
		
		$data = mysqli_query($dbc, $query);	
		echo '<table id="paper_list">';
		echo '<tr id="paper_list_header"><th>Topic</th><th>Paper Name</th><th>Hits</th><th>Rating</th><th>NOQ</th>'.
		'<th>Date</th></tr>';
		if(mysqli_num_rows($data)!=0 ) 
		{
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<tr>';
				echo '<td>'.$row['tname'].'</td>';
				echo '<td>'.$row['name'].'</td>';
				echo '<td>'.$row['hits'].'</td>';
				echo '<td>'.$row['rating'].'</td>';
				echo '<td>'.$row['noq'].'</td>';
				echo '<td>'.$row['date'].'</td>';
				echo '</tr>';			
			}
		}
		else
		{
			echo '<tr><td style="text-align: center;" colspan="6" >No Papers</td></tr>';
		}
		echo '</table>';
		}
		else
		{
			echo '<p class="error">User does not exist.</p>';
		}
	}
	else
	{
		$query = "SELECT username, first_name, last_name, gender FROM user_info WHERE user_id=".$_SESSION['user_id'];
		
		$data = mysqli_query($dbc, $query)or die('<p class="error">There is seems to be a problem.</p>');
		$row = mysqli_fetch_array($data);
		echo '<h3 class="heading">My Profile</h3>';
		echo '<table id="info">';
		echo '<tr><td class="field">Username</td><td class="value">'.$row['username'].'</td></tr>';
		echo '<tr><td class="field">Name</td><td class="value">'.$row['first_name'].' '.$row['last_name'].'</td></tr>';
		echo '<tr><td class="field">Gender</td><td class="value">';
		if($row['gender']=='M')
			echo 'Male';
		else 
			echo 'Female';
		echo '</td></tr>';
		echo '</table>';
		echo '<a class="seeMore" href="editprofile.php">Edit Profile</a>';
		
		$query = "SELECT user_info.username AS author,ppr.name AS paper_name,topic.name AS topic_name,ppr.noq, ".
		"test.correct, test.wrong, test.marks, test.date_time FROM user_test AS test INNER JOIN paper_info AS ".
		"ppr using(paper_id) INNER JOIN topic using (topic_id) INNER JOIN user_info ON ".
		"user_info.user_id=ppr.user_id WHERE test.user_id=".$_SESSION['user_id']." ORDER BY date_time DESC LIMIT 10"; 
		
		$data=mysqli_query($dbc, $query);
		echo '<h3 class="heading">Previous Test</h3><div id="test">';
		echo '<table id="paper_list">';
		echo '<tr id="paper_list_header"><th>Author</th><th>Topic</th><th>Paper Name</th><th>NOQ</th><th>Correct</th><th>Wrong</th>';
		echo '<th>Marks</th><th>Time</th></tr>';
		if(mysqli_num_rows($data)!=0 ) 
		{
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<tr>';
				echo '<td>'.$row['author'].'</td>';
				echo '<td>'.$row['topic_name'].'</td>';
				echo '<td>'.$row['paper_name'].'</td>';
				echo '<td>'.$row['noq'].'</td>';
				echo '<td>'.$row['correct'].'</td>';
				echo '<td>'.$row['wrong'].'</td>';
				echo '<td>'.$row['marks'].'</td>';
				echo '<td>'.$row['date_time'].'</td>';
				echo '</tr>';
			}
		}
		else
		{
			echo '<tr><td style="text-align: center;" colspan="8" >No Papers</td></tr>';
		}
		echo '</table></div>';		 
		
	}
	mysqli_close($dbc);
	require_once('../include/footer.php');
?>