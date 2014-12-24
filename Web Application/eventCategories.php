<!-- 
Cloud Computing Final Project - Event Recommendation System
The event categories that are either recommended or suggested to the user.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();

if (isset($_SESSION["fb_token"])) {
	require_once "database.php";
	$type = 'recommended';
	if(!isset($_GET['type'])) {
		header("location:welcome.php");	
	}
	$type = $_GET['type'];
	if($type == 'recommended'){
		$fid = $_SESSION["fid"];
		$sql = "SELECT cid,rating from prior_rating where fid = '$fid' and rating>=2 order by rating desc";
		$result = $conn->query($sql);
		$outputButtons='';
		while($row = $result->fetch_assoc()) {
			 $outputButtons .= "<a href = 'events.php?id=" . $row["cid"] . "'><button class='btn btn-large btn-block btn-primary' type='button'>".$row["cid"]."</button></a></br></br>";
		}
	}
	elseif ($type == 'suggested') {
		$fid = $_SESSION["fid"];
		$sql = "SELECT cid, rating from prior_rating where fid = '$fid' and rating>=2 order by rating desc";
		$result = $conn->query($sql);
		$recommended = array();
		while($row = $result->fetch_assoc()) {
			array_push($recommended, $row["cid"]);
		}

		$sql = "SELECT cid, rating from post_rating where fid = '$fid' and rating>=4 order by rating desc";
		$result = $conn->query($sql);
		$outputButtons='';
		while($row = $result->fetch_assoc()) {
		
			if(!in_array($row["cid"], $recommended)){
				$outputButtons .= "<a href = 'events.php?id=" . $row["cid"] . "'><button class='btn btn-large btn-block btn-primary' type='button'>".$row["cid"]."</button></a></br></br>";		
			}
		}
	}
	mysqli_close($conn);
}
else{
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Bootstrap, from Twitter</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="css/bootstrap.css" rel="stylesheet">
		<style>
		body {
			padding-top: 60px;
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
							<li><a href="eventtype.php">Events</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
		<?php
		echo $outputButtons;
		?>
		</div>
	</body>
</html>