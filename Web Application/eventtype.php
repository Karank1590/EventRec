<!-- 
Cloud Computing Final Project - Event Recommendation System
Presents the level of recommendation to be selected.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();
if (!isset( $_SESSION["fb_token"])) {
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="css/bootstrap.css" rel="stylesheet">
		<style>
		body {
			padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
		}
		</style>
		<link href="css/bootstrap-responsive.css" rel="stylesheet">
	</head>

	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="brand" href="welcome.php">EventRec</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li ><a href="welcome.php">Home</a></li>
							<li class="active"><a href="eventtype.php">Events</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<a href = 'eventCategories.php?type=recommended'>
				<button class="btn btn-large btn-block btn-primary" type="button">Recommended Events</button>
			</a>
			</br>
			</br>
			<a href = 'eventCategories.php?type=suggested'>
				<button class="btn btn-large btn-block btn-primary" type="button">Suggested Events</button>
			</a>
		</div>   
	</body>
</html>