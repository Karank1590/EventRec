<!-- 
Cloud Computing Final Project - Event Recommendation System
The index page which contains the facebook login.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();

require_once 'autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;


require_once "database.php";

FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);

$fid;
$helper = new FacebookRedirectLoginHelper(REDIRECT_LOGIN);
try {
	$session = $helper->getSessionFromRedirect();
	$_SESSION["user"] = $session;
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}

$userExists = 0;

if (isset($session)) {
	$_SESSION['fb_token'] = $session->getToken();
	$request = new FacebookRequest( $session, 'GET', '/me?fields=id,first_name,last_name' );
	$response = $request->execute();
	$graphObject = $response->getGraphObject();
	$fid = $graphObject->getProperty("id");
	$_SESSION["fid"] = $fid;  
	$ffname = $graphObject->getProperty("first_name");
	$flname = $graphObject->getProperty("last_name");
	$sql = "SELECT * FROM User WHERE fid = '$fid'";
	if($result=mysqli_query($conn, $sql)) {
		$count=mysqli_num_rows($result);
		if($count==1) {
			$userExists = 1;
		}
		else {
			$insert = "insert into User (fid, ffname, flname) values ('$fid', '$ffname', '$flname')";
			$insert_result = mysqli_query($conn,$insert);
			if($insert_result == true) {
				$userExists = 1;
			}
		}
	}

	if ($userExists == 1){
		$_SESSION["userRealName"] = $ffname . " " . $flname;
		$pictureUrl = "https://graph.facebook.com/" . $fid . "/picture";
		$insertsql = "INSERT ignore INTO ProfilePic(fid, url) VALUES (?,?);";
		$stmt = mysqli_prepare($conn, $insertsql);
		$stmt->bind_param("ss", $fid, $pictureUrl);
		$stmt->execute();
		$stmt->close();
		$_SESSION["userProfilePicture"] = "";
		$_SESSION["userProfilePicture"] = $pictureUrl;
		header("location:welcome.php");  
	}
	mysqli_close($conn);
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>EventRec</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<style>
	
	body {
		padding-bottom: 40px;
		color: #5a5a5a;
	}

	.navbar-wrapper {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		z-index: 10;
		margin-top: 20px;
		margin-bottom: -90px;
	}
	
	.navbar-wrapper .navbar {
	}
	
	.navbar .navbar-inner {
		border: 0;
		-webkit-box-shadow: 0 2px 10px rgba(0,0,0,.25);
		-moz-box-shadow: 0 2px 10px rgba(0,0,0,.25);
		box-shadow: 0 2px 10px rgba(0,0,0,.25);
	}

	.navbar .brand {
		padding: 14px 20px 16px;
		font-size: 16px;
		font-weight: bold;
		text-shadow: 0 -1px 0 rgba(0,0,0,.5);
	}

	.navbar .nav > li > a {
		padding: 15px 20px;
	}

	.navbar .btn-navbar {
		margin-top: 10px;
	}

	.carousel {
		margin-bottom: 60px;
	}

	.carousel .container {
		position: relative;
		z-index: 9;
	}

	.carousel-control {
		height: 80px;
		margin-top: 0;
		font-size: 120px;
		text-shadow: 0 1px 1px rgba(0,0,0,.4);
		background-color: transparent;
		border: 0;
		z-index: 10;
	}

	.carousel .item {
		height: 500px;
	}

	.carousel img {
		position: absolute;
		top: 0;
		left: 0;
		min-width: 100%;
		height: 500px;
	}

	.carousel-caption {
		margin-left: auto;
		margin-right: auto;
		background-color: transparent;
		position: static;
		max-width: 550px;
		padding: 0 20px;
		margin-top: 200px;
	}

	.carousel-caption h1, .carousel-caption .lead {
		margin-left: auto;
		margin-right: auto;
		margin: 0;
		line-height: 1.25;
		color: #fff;
		text-shadow: 0 1px 1px rgba(0,0,0,.4);
	}

	.carousel-caption .btn {
		margin-top: 10px;
	}

	.marketing .span4 {
		text-align: center;
	}
	
	.marketing h2 {
		font-weight: normal;
	}
	
	.marketing .span4 p {
		margin-left: 10px;
		margin-right: 10px;
	}

	.featurette-divider {
		margin: 80px 0;
	}

	.featurette {
		padding-top: 120px;
		overflow: hidden;
	}

	.featurette-image {
		margin-top: -120px;
	}

	.featurette-image.pull-left {
		margin-right: 40px;
	}

	.featurette-image.pull-right {
		margin-left: 40px;
	}

	.featurette-heading {
		font-size: 50px;
		font-weight: 300;
		line-height: 1;
		letter-spacing: -1px;
	}

	@media (max-width: 979px) {

		.container.navbar-wrapper {
			margin-bottom: 0;
			width: auto;
		}
		.navbar-inner {
			border-radius: 0;
			margin: -20px 0;
		}

		.carousel .item {
			height: 500px;
		}
		
		.carousel img {
			width: auto;
			height: 500px;
		}

		.featurette {
			height: auto;
			padding: 0;
		}

		.featurette-image.pull-left, .featurette-image.pull-right {
			display: block;
			float: none;
			max-width: 40%;
			margin: 0 auto 20px;
		}
	}

	@media (max-width: 767px) {

		.navbar-inner {
			margin: -20px;
		}

		.carousel {
			margin-left: -20px;
			margin-right: -20px;
		}

		.carousel .container {

		}

		.carousel .item {
			height: 300px;
		}

		.carousel img {
			height: 300px;
		}

		.carousel-caption {
			width: 65%;
			padding: 0 70px;
			margin-top: 100px;
		}

		.carousel-caption h1 {
			font-size: 30px;
		}

		.carousel-caption .lead, .carousel-caption .btn {
			font-size: 18px;
		}

		.marketing .span4 + .span4 {
			margin-top: 40px;
		}

		.featurette-heading {
			font-size: 30px;
		}
		
		.featurette .lead {
			font-size: 18px;
			line-height: 1.5;
		}
	}
	</style>
	</head>
	<body>
		<div class="navbar-wrapper">
			<div class="container">
				<div class="navbar navbar-inverse">
				</div>
			</div>
		</div>

		<div id="myCarousel" class="carousel slide">
			<div class="carousel-inner">
				<div class="item active">
					<img src="img/1.jpg" alt="">
					<div class="container">
						<div class="carousel-caption">
							<h1 class="text-center">EventRec</h1>
							<p class="lead" class="text-center">A Great Way to see Events happening around you!</p>
						</div>
					</div>
				</div>
				<div class="item">
					<img src="img/2.jpg" alt="">
					<div class="container">
						<div class="carousel-caption">
							<h1 class="text-center">Have a Facebook account?</h1>
						</div>
					</div>
				</div>
				<div class="item">
					<img src="img/3.jpg" alt="">
					<div class="container">
						<div class="carousel-caption">
							<h1 class="text-center">See where others are going!</h1>
						</div>
					</div>
				</div>
			</div>
			<a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>

		<div class="container marketing">
			<div class="row">
				<a <?php
				$showLogin = true;
				echo 'href="' . $helper->getLoginUrl() . '"';
				?>
				>
					<button class="btn btn-large btn-block btn-primary" type="button">Login Using Facebook</button>
				</a>

				<h1 align="center">EventRec</h1>
				<p>To login please contact us to be added to the project</p>
			</div>
		</div>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
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
		<script>
		!function ($) {
			$(function(){
				$('#myCarousel').carousel()
			})
		}(window.jQuery)
		</script>
		<script src="js/holder.js"></script>
	</body>
</html>