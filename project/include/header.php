	</head>
	<body>
		<div id="main">
			<header>
				<img id="logo" src="<?php echo ROOT; ?>/images/logo.png" alt="image_of_logo" height="128px" width="400px" />
				<?php
				/* <img id="tagline" src="<?php echo ROOT; ?>/images/tagline.jpg" alt="image_of_tagline" height="75px" width="400px" />*/
				
					if(isLoggedIn()) 
					{
						echo '<span id="status">User: '.$_SESSION['username'].' [<a href="'.ROOT.'/logout.php">Logout</a>]</span>';						
					}
					else 
					{
						echo '<span id="status">User: Guest</span>';
					}
				?>
			</header>