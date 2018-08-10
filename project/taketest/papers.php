<?php
	$page_title = 'Take Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
	?>
	<script type="text/javascript">
		window.onload = function () {
			var list = document.getElementsByClassName("link");
			for (var i=0 ;i <list.length;i++) {
				list[i].onclick = listHandler;
			}
			if(document.getElementById("abookmark"))
			{
				document.getElementById("abookmark").onclick = addBookmark;
			} else if (document.getElementById("rbookmark")) {
				document.getElementById("rbookmark").onclick = removeBookmark;				
			}
		};
		function addBookmark (eventObj) {
					var bookmark = eventObj.target;
					var topic = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/addbookmark.php?type=2&bid='+topic,true);
					bookmarkRequest.send();					
				}
			function removeBookmark (eventObj) {
					var bookmark = eventObj.target;
					var topic = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/removebookmark.php?type=2&bid='+topic,true);
					bookmarkRequest.send();					
				};
		function listHandler (eventObj) {
			var selectedList = eventObj.target;
			var link = selectedList.getAttribute("value");
			window.location = link;
		}
	</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	
	//Connecting to database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	if(isset($_GET['topic'])) 
	{
		$query = "SELECT name FROM topic WHERE topic_id =".$_GET['topic'];
		$data = mysqli_query($dbc, $query)
		or die('Error');
		$row = mysqli_fetch_array($data);
		echo '<h3 class="heading">'.$row['name'].' papers</h3>';
		if(isLoggedIn()) 
		{
			
			$userid = $_SESSION['user_id'];
			$topicid = mysqli_real_escape_string($dbc, trim($_GET['topic']));
			
			$query = "SELECT * FROM bookmark_topic WHERE user_id=$userid AND topic_id=$topicid";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)!=1)
			{
				echo '<div style="float: left;"><button id="abookmark" value="'.$topicid.'">Add to Bookmarks</button></div>';
			}
			else 
			{
				echo '<div style="float: left;"><button  id="rbookmark" value="'.$topicid.'">Remove from Bookmarks</button></div>';
			}
		}
			
		$query = "SELECT ppr.paper_id, ppr.name, ppr.noq, ppr.rating, ppr.hits, ppr.date, ".
		"user_info.username FROM paper_info AS ppr INNER JOIN user_info using(user_id) WHERE ppr.topic_id=".$_GET['topic']." AND published='Y' ORDER BY date DESC";
		
		$data = mysqli_query($dbc, $query);
		
		echo '<table style="clear:both;" id="paper_list"><tr id="paper_list_header"><th>Author</th><th>Paper Name</th><th>NOQ</th><th>Rating</th><th>Hits</th><th>Date</th>'.
		'<th>Take Test</th></tr>';
		if(mysqli_num_rows($data)!=0 ) 
		{
			while($row = mysqli_fetch_array($data)) 
			{
				echo '<tr><td class="link" value="'.ROOT.'/profile/viewprofile.php?user='.$row['username'].'">'.$row['username'].'</td>';
				echo '<td>'.$row['name'].'</td>';
				echo '<td>'.$row['noq'].'</td>';
				echo '<td>'.$row['rating'].'</td>';
				echo '<td>'.$row['hits'].'</td>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td class="link" value="'.ROOT.'/taketest/instructions.php?paper='.$row['paper_id'].'">Click Here</td>';
			}
		}
		else
		{
			echo '<tr><td style="text-align: center;" colspan="7" >No Papers</td></tr>';
		}
		
		echo '</table>';
	}
	else 
	{
		echo '<p class="error" >Please select the topic.</p>';
	}
	require_once('../include/footer.php');	
?>