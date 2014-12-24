<!-- 
Cloud Computing Final Project - Event Recommendation System
The profile page which presents information about the user.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();
require_once "database.php";
if (isset($_SESSION["fb_token"])) {

	$fid = $_SESSION["fid"];
	$sql = "SELECT count(*) as total from PageLike where fid = '$fid'";
	$result = $conn->query($sql);
	$data = $result->fetch_assoc();
	$total = intval($data['total']);

	if($total <= 0){
		$sql = "SELECT count(*) as total from Interests where fid = '$fid'";
		$result = $conn->query($sql);
		$data = $result->fetch_assoc();
		$total = intval($data['total']);
		
		if($total <= 0){
			$sql = "SELECT count(*) as total from Books where fid = '$fid'";
			$result = $conn->query($sql);
			$data = $result->fetch_assoc();
			$total = intval($data['total']);
		
			if($total <= 0){
				$sql = "SELECT count(*) as total from Events where fid = '$fid'";
				$result = $conn->query($sql);
				$data = $result->fetch_assoc();
				$total = intval($data['total']);

				if($total <= 0){
					$sql = "SELECT count(*) as total from Games where fid = '$fid'";
					$result = $conn->query($sql);
					$data = $result->fetch_assoc();
					$total = intval($data['total']);

					if($total <= 0){
						$sql = "SELECT count(*) as total from Movies where fid = '$fid'";
						$result = $conn->query($sql);
						$data = $result->fetch_assoc();
						$total = intval($data['total']);
					
						if($total <= 0){
							$sql = "SELECT count(*) as total from Music where fid = '$fid'";
							$result = $conn->query($sql);
							$data = $result->fetch_assoc();
							$total = intval($data['total']);
						}
					}
				}
			}
		}
	}

	$profileImageSrc = 'src="' . $_SESSION["userProfilePicture"] . '" alt="ScanLine" ';
	$eventsSql = 'select * from MergedEvents , Going where MergedEvents.id1=Going.eid and Going.fid=' . $_SESSION["fid"] ;
	$events = $conn->query($eventsSql);
	$i = 1;
	$outputList = '';
	while($row = mysqli_fetch_array($events)) {
		$id1 = $row["id1"];
		$title = $row["title"];
		$url = $row["eurl"];
		$city = $row["ecity"];
		$outputList .= '<tr><td>' . $i . '</td><td><a href = event.php?id=' . $id1 . ' target=\'_blank\'>' . $title . '</a></td><td>'.$city.'</td></tr>';
		$i = $i+1;
	}
	mysqli_close($conn);
}
else {
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Welcome</title>
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
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="../assets/ico/favicon.png">
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
							<li><a href="collect.php">Collect Interests</a></li>
							<li><a href="eventtype.php">Events</a></li>
							<li><a href="logout.php ">Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-sm-10"><h1><?php echo $_SESSION["userRealName"]?></h1></div>
				<div class="col-sm-2"><a class="pull-right"><img title="profile image" class="img-circle img-responsive" <?php echo $profileImageSrc ?> ></a></div>
			</div>
			<div class="row">
				<div class="col-sm-3"></div>
				<?php
				if($total <= 0){
					echo '<h1>No interests present. Please click on Collect Interests above.</h1>';
				}
				?>
				<div class="col-sm-9">
					<ul class="nav nav-tabs" id="myTab">
						<li class="active"><a href="#home" data-toggle="tab">Events Attending</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="home">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Event</th>
											<th>Location</th>					 
										</tr>
									</thead>
									
									<tbody id="items">
									<?php echo $outputList; ?>				   
									</tbody>
								</table>
								
								<hr>
								
								<div class="row">
									<div class="col-md-4 col-md-offset-4 text-center">
										<ul class="pagination" id="myPager"></ul>
									</div>
								</div>
							</div>

							<hr>

						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap-transition.js"></script>
		<script src="js/bootstrap-alert.js"></script>
		<script src="js/bootstrap-modal.js"></script>
		<script src="js/bootstrap-dropdown.js"></script>
		<script src="js/bootstrap-scrollspy.js"></script>
		<script src="js/bootstrap-tab.js"></script>
		<script src="js/bootstrap-tooltip.js"></script>
		<script src="js/bootstrap-popover.js"></script>
		<script src="js/bootstrap-button.js"></script>
		<script src="js/bootstrap-collapse.js"></script>
		<script src="js/bootstrap-carousel.js"></script>
		<script src="js/bootstrap-typeahead.js"></script>
	</body>
</html>