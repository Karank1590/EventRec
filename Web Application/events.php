<!-- 
Cloud Computing Final Project - Event Recommendation System
The events recommended to the user based on a given category.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();
if (isset($_SESSION["fb_token"])) {
	require_once "database.php";
	if(isset ($_REQUEST["all"])){
		$cid = $_SESSION["cid"];
		$city = '';
		$pn = $_SESSION["pn"];
	}
	else if(isset ($_REQUEST["go"])){
		$cid=$_SESSION["cid"];
		$pn = 1;
		$_SESSION["current_pn"] = $pn;
		$city = $_GET["city"];
		$_SESSION["current_city"] = $city; 
	}
	else{
		$_SESSION["cid"] = $_GET["id"];
		$_SESSION["current_cid"] = $_SESSION["cid"];
		$cid = $_GET['id'];
		if(isset($_GET["pn"])){
			$_SESSION["pn"] = $_GET["pn"];
			$_SESSION["current_pn"] = $_SESSION["pn"];
			$pn = $_GET['pn'];
		}
		else{
			$_SESSION["pn"] = 1;
			$_SESSION["current_pn"] = $_SESSION["pn"];
			$pn = 1;   
		}
		if(isset($_GET["city"])){
			$_SESSION["city"] = $_GET["city"];
			$_SESSION["current_city"] = $_SESSION["city"];
			$city = $_GET['city'];
		}
		else{
			$_SESSION["city"] = '';
			$_SESSION["current_city"] = $_SESSION["city"];
			$city = '';   
		}
	}

	if($city == '' || empty($city)){
		$filterCity = '';
	}
	else{
		$filterCity = " and E.ecity = '" . urldecode($city) . "' ";
	}
	
	$sql = "select * from MergedEvents E , (select * from MergedEventsCategories where cid ='".$cid."' ) temp where E.id1=temp.eid1 " . $filterCity;
	$result = $conn->query($sql);
	$nr = mysqli_num_rows($result);
	if (isset($_GET['pn'])) { 
		$pn = preg_replace('#[^0-9]#i', '', $_GET['pn']);
	}
	else { 
		$pn = 1;
	}

	$itemsPerPage = 10; 
	$lastPage = ceil($nr / $itemsPerPage);
	$centerPages = "";
	$sub1 = $pn - 1;
	$sub2 = $pn - 2;
	$add1 = $pn + 1;
	$add2 = $pn + 2;
	if ($pn == 1) {
		$centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $add1 . '&id='.$cid.'&city='. $city . '">' . $add1 . '</a> &nbsp;';
	}
	else if ($pn == $lastPage) {
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $sub1 . '&id='.$cid.'&city='. $city . '">' . $sub1 . '</a> &nbsp;';
		$centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
	}
	else if ($pn > 2 && $pn < ($lastPage - 1)) {
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $sub2 . '&id='.$cid.'&city='. $city . '">' . $sub2 . '</a> &nbsp;';
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $sub1 . '&id='.$cid.'&city='. $city . '">' . $sub1 . '</a> &nbsp;';
		$centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $add1 . '&id='.$cid.'&city='. $city . '">' . $add1 . '</a> &nbsp;';
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $add2 . '&id='.$cid.'&city='. $city . '">' . $add2 . '</a> &nbsp;';
	}
	else if ($pn > 1 && $pn < $lastPage) {
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $sub1 . '&id='.$cid.'&city='. $city . '">' . $sub1 . '</a> &nbsp;';
		$centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
		$centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $add1 . '&id='.$cid.'&city='. $city . '">' . $add1 . '</a> &nbsp;';
	}

	$limit = 'limit ' .($pn - 1) * $itemsPerPage .',' .$itemsPerPage; 
	$sql = "select * from MergedEvents E , (select * from MergedEventsCategories where cid ='".$cid."' ) temp where E.id1=temp.eid1 " . $filterCity . $limit; 
	$result = $conn->query($sql);
	$paginationDisplay = ""; 
	if ($lastPage != "1"){
		$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage. '&nbsp;  &nbsp;  &nbsp; ';
		if ($pn != 1) {
			$previous = $pn - 1;
			$paginationDisplay .=  '&nbsp;  <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $previous . '&id='.$cid.'&city='. $city . '"> Back</a> ';
		} 
		$paginationDisplay .= '<span class="paginationNumbers">' . $centerPages . '</span>';
		if ($pn != $lastPage) {
			$nextPage = $pn + 1;
			$paginationDisplay .=  '&nbsp;  <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $nextPage . '&id='.$cid.'&city='. $city . '"> Next</a> ';
		} 
	}

	$outputList = '';
	while($row = mysqli_fetch_array($result)){ 
		$id1 = $row["id1"];
		$title = $row["title"];
		$url = $row["eurl"];
		$address = $row["evenuename"];
		$city = $row["ecity"];
		$outputList .= '<h3><a href = event.php?id=' . $id1 . ' target=\'_blank\'>' . $title . '</a> at '.$address.' in '.$city.'</h3>';
	}
}
else {
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Results</title>
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
		<style type="text/css">
		.pagNumActive {
			color: #000;
			border:#060 1px solid; background-color: #D2FFD2; padding-left:3px; padding-right:3px;
		}

		.paginationNumbers a:link {
			color: #000;
			text-decoration: none;
			border:#999 1px solid; background-color:#F0F0F0; padding-left:3px; padding-right:3px;
		}

		.paginationNumbers a:visited {
			color: #000;
			text-decoration: none;
			border:#999 1px solid; background-color:#F0F0F0; padding-left:3px; padding-right:3px;
		}

		.paginationNumbers a:hover {
			color: #000;
			text-decoration: none;
			border:#060 1px solid; background-color: #D2FFD2; padding-left:3px; padding-right:3px;
		}

		.paginationNumbers a:active {
			color: #000;
			text-decoration: none;
			border:#999 1px solid; background-color:#F0F0F0; padding-left:3px; padding-right:3px;
		}
		</style>
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
			<div style="margin-left:64px; margin-right:64px;">
				<h2>Total Items: 
				<?php echo $nr . "          "; 
				$citiesSql = "select distinct (ecity) from MergedEvents order by ecity";
				$result = $conn->query($citiesSql);
				?>
				</h2>
				<form method="GET">
					<select name="city">
					<?php 
					while($row = mysqli_fetch_array($result)) {
						echo "<option value=";
						echo urlencode($row['ecity']);
						echo ">";
						echo $row['ecity'];
						echo "</option>";
					}
					?>  
					</select>
					<input type = "submit" name = "all" value = "All" />
					<input type = "submit" name = "go" value = "Go" />
				</form>
			</div>
			<div style="margin-left:58px; margin-right:58px; padding:6px; background-color:#FFF; border:#999 1px solid;"><?php echo $paginationDisplay; ?></div>
			<div style="margin-left:64px; margin-right:64px;"><?php print "$outputList"; ?></div>
			<div style="margin-left:58px; margin-right:58px; padding:6px; background-color:#FFF; border:#999 1px solid;"><?php echo $paginationDisplay; ?></div>
		</div>
	</body>
</html>