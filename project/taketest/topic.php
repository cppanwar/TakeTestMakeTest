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
			var list = document.getElementsByClassName("select_list");
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
					var category = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/addbookmark.php?type=1&bid='+category,true);
					bookmarkRequest.send();					
				}
			function removeBookmark (eventObj) {
					var bookmark = eventObj.target;
					var category = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/removebookmark.php?type=1&bid='+category,true);
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
	
	if(isset($_GET['category'])) 
	{
		
		
		echo '<h3 class="heading">Choose the topic</h3>';
		if(isLoggedIn()) 
		{
			$userid = $_SESSION['user_id'];
			$categoryid = mysqli_real_escape_string($dbc, trim($_GET['category']));
			
			$query = "SELECT * FROM bookmark_category WHERE user_id=$userid AND category_id=$categoryid";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)!=1)
			{
				echo '<div style="float: left;" ><button id="abookmark" value="'.$categoryid.'">Add to Bookmarks</button></div>';
			}
			else 
			{
				echo '<div style="float: left;" ><button id="rbookmark" value="'.$categoryid.'">Remove from Bookmarks</button></div>';
			}
		}
		$query = "SELECT topic.name,topic.topic_id FROM topic INNER JOIN category_topic using (topic_id)".
		" WHERE category_topic.category_id =". $_GET['category'];
		
		$data = mysqli_query($dbc, $query);
		echo '<ul style="clear: both;">';
		while($row = mysqli_fetch_array($data)) 
		{
			echo '<li class="select_list" value="'.ROOT.'/taketest/papers.php?topic='.$row['topic_id'].'" >'.$row['name'].' &raquo;</li>';
		}
		echo '</ul>';

	}
	else 
	{
		echo '<p class="error" >Please select the category.</p>';
	}
	mysqli_close($dbc);
	require_once('../include/footer.php');
?>