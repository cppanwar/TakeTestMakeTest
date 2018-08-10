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
	
	$query = "SELECT * FROM category";
	$data = mysqli_query($dbc, $query);
		
	echo '<h3 class="heading">Choose the category</h3>';
	echo '<ul>';
	while($row=mysqli_fetch_array($data)) 
	{
		echo '<li class="select_list" value="'.ROOT.'/taketest/topic.php?category='.$row['category_id'].'" >'.$row['name'].' &raquo;</li>';
	}
	echo '</ul>';
	/*
	echo '<div>';
	while($row=mysqli_fetch_array($data)) 
	{
		echo '<a style="display: block; text-decoration: none;" class="select_list" href="topic.php?category='.$row['category_id'].'">'.$row['name'].' &raquo;</a>';
	}
	echo '</div>';*/
	
	mysqli_close($dbc);
	require_once('../include/footer.php');	
?>