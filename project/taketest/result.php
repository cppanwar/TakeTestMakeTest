<?php
	$page_title = 'Take Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
	?>
	<style type="text/css">
		table caption
		{
			background-color: #413CD9;
			padding: 10px;
			font-weight: bold;
		}
		table{
			border-spacing: 1px;
			margin-left: 20px;
			background-color: #413CD9;
			color: white;
			box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
		}
		td{
			
			width: 300px;
			padding: 10px;
			text-align: center;
			
			border-radius: 3px;
		}
		td:last-child{
			width: 100px;
		}
		tr:first-child{
			background-color: #404040;
			color: white;	
		}
		tr:nth-child(2){
			background-color: #03A700;
		}
		tr:nth-child(3){
			background-color: #CD0220;
		}
		tr:nth-child(4){
			background-color: white;
			color: black;
		}
		input{
			margin: 5px;
		}
	</style>
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
					var paper = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/addbookmark.php?type=3&bid='+paper,true);
					bookmarkRequest.send();					
				}
			function removeBookmark (eventObj) {
					var bookmark = eventObj.target;
					var paper = bookmark.getAttribute("value");
					
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
					bookmarkRequest.open('GET',root+'/bookmarks/removebookmark.php?type=3&bid='+paper,true);
					bookmarkRequest.send();					
				};
</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	if(isLoggedIn()) 
	{
		if(isset($_POST['submit']) && $_SESSION['test']==true)
		{
			echo '<h3 class="heading">Result</h3>';
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$userid = $_SESSION['user_id'];
			$paperid = mysqli_real_escape_string($dbc, trim($_POST['paperid']));
			
			$query = "SELECT * FROM bookmark_paper WHERE user_id=$userid AND paper_id=$paperid";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data)!=1)
			{
				echo '<div style="float: left;"><button id="abookmark" value="'.$paperid.'">Add to Bookmarks</button></div>';
			}
			else 
			{
				echo '<div style="float: left;"><button  id="rbookmark" value="'.$paperid.'">Remove from Bookmarks</button></div>';
			}
				
		$_SESSION['test']= false;
		$correct = 0;
		$wrong = 0;
		$qcount = 0;
		
		
		$query = "select question_info.answer, question_info.question, question_info.q_id from question_info inner join question_paper ".
		"using(q_id) where question_paper.paper_id=$paperid";		
		$data = mysqli_query($dbc, $query);
		$qcount = mysqli_num_rows($data);
		
		echo '<ol style="clear: both;" id="testpaper">';
		for($i=1; $i<=$qcount; $i++)
		{	
			$row = mysqli_fetch_array($data);
			echo '<div class="questionSet">';
			echo '<li class="question">'.$row['question'].'</li>';
			echo '<ol >';
		 if(isset($_POST[$row['q_id']])) {
		 		$useranswer=$_POST[$row['q_id']];
			if($useranswer==$row['answer']) 
			{
				echo '<li class="option" style="background-color: #008000; color: white; list-style-type: none;" >'.$_POST[$row['q_id']].'</li>';
				$correct++;
			}
			else 
			{	
				$wrong++;
				echo '<li class="option" style="background-color: #D40000; color: white; list-style-type: none;" >'.$useranswer.'</li>';
				echo '<li class="option" style="background-color: #008000; color: white; list-style-type: none;" >'.$row['answer'].'</li>';
			}
		 }
		 else{
		 	echo '<li class="option" style="background-color: white; list-style-type: none;">'.$row['answer'].'</li>';
		 }
		 echo '</ol>';
		 echo '</div>';
		 echo '<br />';
		}
		echo '</ol>';
		echo '<br />';
		echo '<table><caption>Result</caption>';
		echo '<tr><td>Total no. of questions</td><td>'.$qcount.'</td></tr>';
		echo '<tr><td>Correct</td><td>'.$correct.'</td></tr>';
		echo '<tr><td>Wrong</td><td>'.$wrong.'</td></tr>';
		$marks = ($correct)-($wrong*0.25);
		echo '<tr><td>Total marks</td><td>'.$marks.'</td></tr></table>';
		$_SESSION['paperid'] = $paperid;
		$_SESSION['correct'] = $correct;
		$_SESSION['wrong'] = $wrong;
		$_SESSION['marks'] = $marks;
		
		$query = "SELECT * FROM user_test WHERE paper_id=$paperid AND user_id=".$_SESSION['user_id'];
		$data = mysqli_query($dbc, $query);
		$firsttime = 0;
		if(mysqli_num_rows($data)==0) 
			$firsttime = 1; 
		
		echo '<div id="formContainer" style="width: 440px; margin: 10px;"><form method="post" action="thankyou.php">';
		if($firsttime) 
		{
	?>
			<fieldset>
			<legend>Rate This Test</legend>
			<input type="radio" name="rating" value="1" />1 (Poor)<br />
			<input type="radio" name="rating" value="2" />2 (Fair)<br />
			<input type="radio" name="rating" value="3" checked="checked" />3 (Good)<br />
			<input type="radio" name="rating" value="4" />4 (Very Good)<br />
			<input type="radio" name="rating" value="5" />5 	(Excellent)<br />
			</fieldset>
			
	<?php
		}
		echo '<input id="submitButton" type="submit" name="submit" value="Submit" /></form></div>';
		
	}
	 else
	 {
	 	echo '<p class="error">This page is shown after the test. You didn\'t take the test</p>';
	 }
	}
	else
	{
		echo '<p class="error">You must log in to take test. If you don\'t have an account please sign up.</p>';
	}
	require_once('../include/footer.php');
?>