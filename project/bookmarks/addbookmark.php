<?php
	require_once('../include/sessionstart.php');
	require_once('../include/connectvars.php');
		
	if(isset($_GET['type'])&&isset($_GET['bid'])&&isset($_SESSION['user_id'])) 
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$btype= mysqli_real_escape_string($dbc, trim($_GET['type']));
		$bid= mysqli_real_escape_string($dbc, trim($_GET['bid']));
		$userid = $_SESSION['user_id'];
		if(!empty($btype) && !empty($bid)) 
		{
			if($btype==1) 
			{
				$query = "SELECT * FROM category WHERE category_id=$bid";
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data)==1) 
				{
					$query = "SELECT * FROM bookmark_category WHERE user_id=$userid AND category_id=$bid";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data)==0) 
					{
						$query = "INSERT INTO bookmark_category(user_id , category_id) VALUES ($userid ,$bid )";
						if(mysqli_query($dbc, $query))
						 echo '1'; 
					}
				}
			}
			else if($btype==2) 
			{
				$query = "SELECT * FROM topic WHERE topic_id=$bid";
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data)==1) 
				{
					$query = "SELECT * FROM bookmark_topic WHERE user_id=$userid AND topic_id=$bid";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data)==0) 
					{
						$query = "INSERT INTO bookmark_topic(user_id , topic_id) VALUES ($userid ,$bid )";
						if(mysqli_query($dbc, $query))
						 echo '1'; 
					}
				}
			}else if($btype==3) 
			{
				$query = "SELECT * FROM paper_info WHERE paper_id=$bid";
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data)==1) 
				{
					$query = "SELECT * FROM bookmark_paper WHERE user_id=$userid AND paper_id=$bid";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data)==0) 
					{
						$query = "INSERT INTO bookmark_paper(user_id , paper_id) VALUES ($userid ,$bid )";
						if(mysqli_query($dbc, $query))
						 echo '1'; 
					}
				}
			}else if($btype==4) 
			{
				$query = "SELECT * FROM user_info WHERE username='$bid'";
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data)==1) 
				{
					$query = "SELECT * FROM bookmark_user WHERE user_id=$userid AND b_user='$bid'";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data)==0) 
					{
						$query = "INSERT INTO bookmark_user(user_id , b_user) VALUES ($userid ,'$bid' )";
						if(mysqli_query($dbc, $query))
						 echo '1'; 
					}
				}
			}
		}
	}
?>