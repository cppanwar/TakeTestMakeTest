<!doctype html>
<html>
<head>
	<title>Test Page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
</head>
<body>
<form action="test.php" method="post">
	<fieldset>
		<legend>Test Info</legend>
		Title: <input name="title" type="text" /><br />
		Description: <input name="description" type="text" /><br />
	</fieldset>
	<input type="submit" value="Submit" name="submit" />
</form>
<div>
	<h1>Output</h1>
	<ol>
	<?php
		$dbc = mysqli_connect('127.0.0.1','root','989777_Mysql','testdb')
		or die('Error Connecting DB.');
		
		if(isset($_POST['submit'])) 
		{
			$title = $_POST['title'];
			$description = $_POST['description'];
			$query = "INSERT INTO articles (title, description) VALUES('$title' ,'$description' )";
			mysqli_query($dbc, $query) or die('Error querying DB-'.mysqli_error($dbc));
		}
		
		$query = "select * from articles";
		$data = mysqli_query($dbc, $query);
		while($row=mysqli_fetch_array($data)) 
		{
			echo '<li>Title: '.$row['title'].' Description: '.$row['description'].'</li>';
		}
	?>
	</ol>
</div>
</body>
</html>