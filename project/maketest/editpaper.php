<?php
	$page_title = 'Make Test';
	require_once('../include/constants.php');
	require_once('../include/sessionstart.php');
	require_once('../include/functions.php');
	require_once('../include/connectvars.php');
	require_once('../include/htmltemplate.php');
?>
<script type="text/javascript">
	window.onload = function () {
		document.getElementById("addQuestion").onclick = addQuestion;
	};
	var question, option_a, option_b, option_c, option_d, answer, qcount=1;
	function addQuestion() {
		 question = document.getElementById("newQuestion").value;
		 option_a = document.getElementById("newOption_a").value;
		 option_b = document.getElementById("newOption_b").value;
		 option_c = document.getElementById("newOption_c").value;
		 option_d = document.getElementById("newOption_d").value;
		var radio_option = document.getElementsByName("option");
		 var i;
		for (i= 0; i<4; i++) {
			if(radio_option[i].checked){
				answer = document.getElementById(radio_option[i].value).value;
			}
		}
		
		addToPaper();
		addToForm();
		document.getElementById("newQuestion").value = '';
		document.getElementById("newOption_a").value = '';
		document.getElementById("newOption_b").value = '';
		document.getElementById("newOption_c").value = '';
		document.getElementById("newOption_d").value = '';
	}
	function addToPaper() {
		var li1 = document.createElement("li");
		li1.innerHTML = option_a;
		li1.setAttribute("class","option");
		var li2 = document.createElement("li");
		li2.innerHTML = option_b;
		li2.setAttribute("class","option");
		var li3 = document.createElement("li");
		li3.innerHTML = option_c;
		li3.setAttribute("class","option");
		var li4 = document.createElement("li");
		li4.innerHTML = option_d;
		li4.setAttribute("class","option");
		var li5 = document.createElement("li");
		li5.innerHTML = answer;
		li5.setAttribute("class","option");
		li5.setAttribute("style","list-style-type: none; background-color: #99ff66;");
		var ol = document.createElement("ol");
		ol.appendChild(li1);
		ol.appendChild(li2);
		ol.appendChild(li3);
		ol.appendChild(li4);
		ol.appendChild(li5);
		var questionli = document.createElement("li");
		questionli.innerHTML = question;
		questionli.setAttribute("class","question");
		var div = document.createElement("div");
		div.appendChild(questionli);
		div.appendChild(ol);
		div.setAttribute("class","questionSet");
		document.getElementById("testpaper").appendChild(div);
	}
	
	function addToForm() {
		var textarea = document.createElement("textarea");
		textarea.setAttribute("name", "question"+qcount);
		textarea.innerHTML=question;
		
		var input1 = document.createElement("input");
		input1.setAttribute("type","text");
		input1.setAttribute("name", qcount+"_a");
		input1.setAttribute("value",option_a);

		var input2 = document.createElement("input");
		input2.setAttribute("type","text");
		input2.setAttribute("name", qcount+"_b");
		input2.setAttribute("value",option_b);

		var input3 = document.createElement("input");
		input3.setAttribute("type","text");
		input3.setAttribute("name", qcount+"_c");
		input3.setAttribute("value",option_c);

		var input4 = document.createElement("input");
		input4.setAttribute("type","text");
		input4.setAttribute("name", qcount+"_d");
		input4.setAttribute("value",option_d);

		var input5 = document.createElement("input");
		input5.setAttribute("type","text");
		input5.setAttribute("name", qcount+"_answer");
		input5.setAttribute("value",answer);
		
		var fieldset = document.getElementById("hElement");
		fieldset.appendChild(textarea);
		fieldset.appendChild(input1);
		fieldset.appendChild(input2);
		fieldset.appendChild(input3);
		fieldset.appendChild(input4);
		fieldset.appendChild(input5);
		qcount++;		
	}
</script>
<?php
	require_once('../include/header.php');
	require_once('../include/navigation.php');
	
	if(isLoggedIn()) 
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$userid = $_SESSION['user_id'];
		if(isset($_POST['savepaper'])) 
		{
			$paper = mysqli_real_escape_string($dbc, trim($_POST['paper']));
			$query = "SELECT * FROM paper_info WHERE user_id= $userid AND paper_id=$paper AND published='N' ";
			$data = mysqli_query($dbc, $query);
			
			if(mysqli_num_rows($data) == 1) 
			{
				$row = mysqli_fetch_array($data);
				$noq = $row['noq']; 
				$i = 1;
				$pnoq = $noq;
				echo '<h3 class="heading">Questions Saved to '.$row['name'].'</h3>';
				echo '<ol id="testpaper">';
				while(isset($_POST['question'.$i])) 
				{
					$question = mysqli_real_escape_string($dbc,strip_tags(trim($_POST['question'.$i])));
					$option_a = mysqli_real_escape_string($dbc,strip_tags(trim($_POST[$i.'_a'])));
					$option_b = mysqli_real_escape_string($dbc,strip_tags(trim($_POST[$i.'_b'])));
					$option_c = mysqli_real_escape_string($dbc,strip_tags(trim($_POST[$i.'_c'])));
					$option_d = mysqli_real_escape_string($dbc,strip_tags(trim($_POST[$i.'_d'])));
					$answer = mysqli_real_escape_string($dbc,strip_tags(trim($_POST[$i.'_answer'])));
					
					if(!empty($question) && !empty($option_a) && !empty($option_b) && !empty($option_c) && !empty($option_d) && !empty($answer))
					{
						$query = "INSERT INTO question_info(question, option_a, option_b, option_c, option_d, answer )".
						" VALUES ('$question' ,'$option_a','$option_b','$option_c' ,'$option_d' ,'$answer' )";
						if(mysqli_query($dbc, $query))
						{						
							$query = "INSERT INTO question_paper(paper_id, q_id) VALUES($paper , LAST_INSERT_ID())";
							if(mysqli_query($dbc, $query))
							{
								$noq++;
								$query = "UPDATE paper_info SET noq=$noq WHERE paper_id=$paper ";
								if(mysqli_query($dbc, $query))
								{
									echo '<div class="questionSet">';
									echo '<li class="question">'.$question.'</li>';
									echo '<ol>';
									echo '<li class="option">'.$option_a.'</li>';
									echo '<li class="option">'.$option_b.'</li>';
									echo '<li class="option">'.$option_c.'</li>';
									echo '<li class="option">'.$option_d.'</li>';
									echo '<li style="list-style-type: none; background-color: #99ff66;" class="option">'.$answer.'</li>';
									echo '</ol></div>';
								}
							}
						}
						else
						{
							echo '<p>Error adding question:'; 
							$error1 = mysqli_error($dbc);
							echo $error1.'</p>'; 
						}
						
						
					}
					if($noq>30)
					{
						echo '<p class="error">Paper can\'t contain more than 30 question.</p>';
						break;
					}
					$i++;
				}
				$i--;
				echo '</ol>';
				if($skip =($i-($noq-$pnoq))) 
				echo '<p class="error">'.$skip.' questions has not been saved in paper.</p>';
				
			}
			else 
			{
				echo '<p class="error">Paper does not exist.</p>';
			}
			
			require_once('../include/footer.php');
			exit();
		}
		if(isset($_GET['paper'])) 
		{
			//---------------------------Input data-------------------------
			$paperid = mysqli_real_escape_string($dbc, trim($_GET['paper']));
			
			$query = "SELECT * FROM paper_info WHERE user_id= $userid AND paper_id=$paperid AND published='N'";
			$data = mysqli_query($dbc, $query);
			
			if(mysqli_num_rows($data) == 1) 
			{	
				$row = mysqli_fetch_array($data);
				echo '<h3 class="heading">Edit Paper - '.$row['name'].'</h3>';
				$query = "select question_info.question, question_info.option_a, question_info.option_b, question_info.option_c, ".
				"question_info.option_d,question_info.answer from question_info inner join question_paper ".
				" using(q_id) where question_paper.paper_id=$paperid";
				
				$data = mysqli_query($dbc, $query) or die('<p class="error">There is some problem loading paper.</p>');
				echo '<ol id="testpaper" style="min-height: 100px ;max-height:400px; overflow: auto; border: 1px solid black; border-radius: 5px;">';
				while($row=mysqli_fetch_array($data)) 
				{
					echo '<div class="questionSet">';
					echo '<li class="question">'.$row['question'].'</li>';
					echo '<ol>';
					echo '<li class="option">'.$row['option_a'].'</li>';
					echo '<li class="option">'.$row['option_b'].'</li>';
					echo '<li class="option">'.$row['option_c'].'</li>';
					echo '<li class="option">'.$row['option_d'].'</li>';
					echo '<li style="list-style-type: none; background-color: #99ff66;" class="option">'.$row['answer'].'</li>';
					echo '</ol></div>';
				}				
				echo '</ol>';
?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
					<fieldset id="hElement" style="display:none;">
						<!--
							<textarea name="question1">test</textarea>
							<input value="test1" name="1_a" type="text">
							<input value="test2" name="1_b" type="text">
							<input value="test3" name="1_c" type="text">
							<input value="test4" name="1_d" type="text">
							<input value="test4" name="1_answer" type="text">
						-->
						<input type="hidden" name="paper" value="<?php echo $paperid ?>" />
					</fieldset>
					<input type="submit" value="Save Paper" name="savepaper" />
				</form>
				
				<form>
					<fieldset>
						<textarea id="newQuestion" name="question" rows="5" cols="71"></textarea><br />
						<input type="radio" name="option" value="newOption_a" />
						<input id="newOption_a" type="text" name="a" /><br />
						<input type="radio" name="option" value="newOption_b" />
						<input id="newOption_b" type="text" name="b" /><br />
						<input type="radio" name="option" value="newOption_c" />
						<input id="newOption_c" type="text" name="c" /><br />
						<input type="radio" name="option" value="newOption_d" />
						<input id="newOption_d" type="text" name="d" /><br />
					</fieldset>
					<input id="addQuestion" type="button" value="Add Question" />
				</form>
<?php
			}
			else 
			{
				echo '<p class="error">Paper does not exist.</p>';
				require_once('../include/footer.php');
				mysqli_close($dbc);
				exit();
			}
		}
		else 
		{
			echo '<p class="error">Select Paper to edit paper. <a href="mypapers.php">Click here</a> to select paper.</p>';
		}
		mysqli_close($dbc);
	}
	else
	{
		echo '<p class="error">Please Log-In to access this page</p>';
	}
	require_once('../include/footer.php');
?>