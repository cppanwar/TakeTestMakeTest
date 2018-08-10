<?php
	$page_title = 'Take Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
	?>
	<script type="text/javascript">
		var i;
		var option = document.getElementsByClassName("option");
		var radioOption = document.getElementsByClassName("radioOption");
		var clearButton = document.getElementsByClassName("clearButton");
		
		window.onload = function () 
		{
			for (i=0;i<option.length;i++) 
			{
				option[i].onclick = optionHandler;
			}
			for (i=0 ;i<clearButton.length; i++) 
			{
				clearButton[i].onclick = clearOption;
			}
		};
	
		function optionHandler(eventObj) 
		{
			var currentOption = eventObj.target;
			var qno = currentOption.getAttribute("name");
			var selectedOption = currentOption.innerHTML;
			console.log(selectedOption);
			var count=1;
			for (i=0; i<option.length; i++) 
			{
				if (option[i].getAttribute("name") == qno) 
				{
					option[i].style.backgroundColor = "#f7f7f7";
					count++;
				}
				if (option[i].getAttribute("name") == qno && option[i].innerHTML == selectedOption) 
				{
					radioOption[i].checked = true;
					currentOption.style.backgroundColor = "#ffffcc";
					
				}
				if (count===5)
					break;
				
			}
			for (i=0; i<clearButton.length; i++) 
			{
				if (clearButton[i].getAttribute("name")==qno) 
				{
					clearButton[i].setAttribute("value",selectedOption);
				}				
			}
		}
		
		function clearOption(eventObj) 
		{
			var cbutton = eventObj.target;
			var qno = cbutton.getAttribute("name");
			var selectedOption = cbutton.getAttribute("value");
			for (i=0; i<option.length; i++) 
			{
				if (option[i].getAttribute("name") == qno && option[i].innerHTML == selectedOption)
				{
					radioOption[i].checked=false;
					option[i].style.backgroundColor= "#f7f7f7";
					break;
				}
			}
		}
	</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	if(isLoggedIn()) 
	{
		if(isset($_POST['submit'])) 
		{
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$paperid = $_POST['paper'];
			$_SESSION['test'] = true;
			$query = "select question_info.question, question_info.option_a, question_info.option_b, question_info.option_c, ".
			"question_info.option_d, question_info.q_id from question_info inner join question_paper ".
			" using(q_id) where question_paper.paper_id=$paperid";
		
			$data = mysqli_query($dbc, $query);
			$qno=1;
			echo '<h3 class="heading">Test</h3>';
			echo '<ol id="testpaper">';
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<div class="questionSet">';
				echo '<li class="question">'.$row['question'].'</li>';
				echo '<ol>';
				echo '<li class="option" name="'.$row['q_id'].'">'.$row['option_a'].'</li>';
				echo '<li class="option" name="'.$row['q_id'].'">'.$row['option_b'].'</li>';
				echo '<li class="option" name="'.$row['q_id'].'">'.$row['option_c'].'</li>';
				echo '<li class="option" name="'.$row['q_id'].'">'.$row['option_d'].'</li>';
				echo '<button class="clearButton" name="'.$row['q_id'].'" value="">Clear Response</button>';
				echo '</ol>';	
				echo '</div>';
				echo '<br />';
				$qno++;
			}
			echo '</ol>';
			echo '<form action="result.php" method="post">';
			
			$data = mysqli_query($dbc, $query);
			while($row=mysqli_fetch_array($data)) 
			{
				echo '<div style="display: none;"><fieldset>';
				echo '<input class="radioOption" type="radio" value="'.$row['option_a'].'" name="'.$row['q_id'].'" />'.$row['option_a'].'<br />';
				echo '<input class="radioOption" type="radio" value="'.$row['option_b'].'" name="'.$row['q_id'].'" />'.$row['option_b'].'<br />';
				echo '<input class="radioOption" type="radio" value="'.$row['option_c'].'" name="'.$row['q_id'].'" />'.$row['option_c'].'<br />';
				echo '<input class="radioOption" type="radio" value="'.$row['option_d'].'" name="'.$row['q_id'].'" />'.$row['option_d'].'<br />';
				echo '</fieldset></div>';
			
			}
			echo '<input type="hidden" name="paperid" value="'.$paperid.'" />';
			echo '<input type="submit" name="submit" value="Submit" />';
			echo '</form>';
				}
		else 
		{
			echo '<p class="error">Select the topic first to participate in test.</p>';
		}
	}
	else
	{
		echo '<p class="error">You must log in to take test. If you don\'t have an account please sign up.</p>';
	}
	require_once('../include/footer.php');
?>