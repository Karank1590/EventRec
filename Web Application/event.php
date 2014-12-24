<!-- 
Cloud Computing Final Project - Event Recommendation System
Presents the information and links about the event and the venue, additionally, presents information from Yelp about the venue.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();

if (isset($_SESSION["fb_token"])) {
	require_once "database.php";
	require_once('lib/OAuth.php');
	$eventId = $_GET['id'];	
	if(isset($_REQUEST["going"])){
		if($_REQUEST["going"] == "Attend"){
			$insertSql = "insert into Going(eid, fid) VALUES ('" . $eventId . "','" .$_SESSION["fid"]. "')";
			$result = $conn->query($insertSql); 
		}
		else{
			$insertSql = "delete from Going where eid='" . $eventId . "' and fid='" .$_SESSION["fid"]. "'";
			$result = $conn->query($insertSql); 
		}
	}
	$sql = "select * from MergedEvents where id1 = '" . $eventId . "'"; 
	$result = $conn->query($sql);
	
	if(mysqli_num_rows($result) != 1) {
		header("location:welcome.php");
	}

	$event = mysqli_fetch_array($result);
	$eventDesc = trim($event["edesp"]);
	$title = trim($event["title"]);
	$eventUrl = trim($event["eurl"]);
	$eCity = trim($event["ecity"]);
	$eState = trim($event["estate"]);
	$eCountry = trim($event["ecounter"]);
	$ePostcode = trim($event["epostcode"]);
	$eVenueId = trim($event["evenueid"]);
	$eVenueName = trim($event["evenuename"]);
	$eVenueUrl = trim($event["evenueurl"]);
	$eLat = floatval(trim($event["elat"]));
	$eLng = floatval(trim($event["elng"]));
	$eStime = trim($event["estime"]);
	$coordinates =  $eLat . "," . $eLng;
	$eVenueAdd = trim($event["evenueadd"]);

	$CONSUMER_KEY = YELP_CONSUMER_KEY;
	$CONSUMER_SECRET = YELP_CONSUMER_SECRET;
	$TOKEN = YELP_TOKEN;
	$TOKEN_SECRET = YELP_TOKEN_SECRET;
	$API_HOST = 'api.yelp.com';
	$DEFAULT_TERM = $eVenueName;
	$DEFAULT_LOCATION = $eState . ' ' . $ePostcode;
	$SEARCH_LIMIT = 1;
	$SEARCH_PATH = '/v2/search/';
	$BUSINESS_PATH = '/v2/business/';

	function request($host, $path) {
		$unsigned_url = "http://" . $host . $path;
		$token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);
		$consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		$oauthrequest = OAuthRequest::from_consumer_and_token(
			$consumer, 
			$token, 
			'GET', 
			$unsigned_url
		);
		$oauthrequest->sign_request($signature_method, $consumer, $token);
		$signed_url = $oauthrequest->to_url();
		$ch = curl_init($signed_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);	
		return $data;
	}

	function search($term, $location) {
		$url_params = array();
		$url_params['term'] = $term ?: $GLOBALS['DEFAULT_TERM'];
		$url_params['location'] = $location?: $GLOBALS['DEFAULT_LOCATION'];
		$url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
		$search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params);	
		return request($GLOBALS['API_HOST'], $search_path);
	}

	function get_business($business_id) {
		$business_path = $GLOBALS['BUSINESS_PATH'] . $business_id;
		return request($GLOBALS['API_HOST'], $business_path);
	}

	function query_api($term, $location) {     
		$response = json_decode(search($term, $location));
		$business_id = $response->businesses[0]->id;
		return $response;
	}

	$longopts  = array(
		"term::",
		"location::",
	);
	$options = getopt("", $longopts);
	$term = $options['term'] ?: '';
	$location = $options['location'] ?: '';
	$response =  query_api($term, $location);
	$eVenueYelp = $response->businesses[0]->url;
	$eVenueYelpRating = $response->businesses[0]->rating_img_url;
	$eVenuePhone = $response->businesses[0]->display_phone;
	$eVenueImage = $response->businesses[0]->image_url;
	$goingSql = "select fid from Going where eid='" . $eventId . "' and fid='" .$_SESSION["fid"]. "'";
	$result = $conn->query($goingSql);
	if(mysqli_num_rows($result) != 1) {
		$buttonValue = false;
	}
	else {
		$buttonValue = true;
	}
	$usersAttendingSql = "select fid from Going where eid='" . $eventId . "'";
	$users=$conn->query($usersAttendingSql);
	mysqli_close($conn);
}
else {
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Event</title>
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
		<style type="text/css">
		html, body, #map-canvas {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		</style>
		<script src="http://maps.googleapis.com/maps/api/js"></script>
		<script>
		var myCenter=new google.maps.LatLng(
		<?php
		echo $coordinates;
		?>
		);
		function initialize() {
			var mapProp = {
				center: myCenter,
				zoom:5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
			var marker = new google.maps.Marker({
				position: myCenter,
				title:'Click to zoom'
			});
			marker.setMap(map);
			google.maps.event.addListener(marker,'click',function() {
				map.setZoom(9);
				map.setCenter(marker.getPosition());
			});
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		</script>
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
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
					<div class="panel panel-info">
						<div class="panel-heading">
							</br>
							</br> 
							<h3 class="panel-title"><?php echo "<a href = '" . $eventUrl . "' target='_blank'>" . $title . "</a>"; ?></h3>
							<form method="post">
								<input type="submit" name="going" id="going" class ="btn btn-primary" value=
								<?php 
								if($buttonValue == false){
									echo "Attend";
								} 
								else{
									echo "Withdraw";
								}
								?>
								/>
							</form>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-3 col-lg-3 " align="center"><img src=
								<?php
								echo "'".$eVenueImage."'";
								?> 
								>
								</div>
								</br>
								<div class=" col-md-9 col-lg-9 ">
									<table class="table table-user-information">
										<tbody>
											<tr>
												<td>Description:</td>
												<td><?php echo $eventDesc; ?></td>
											</tr>
											<tr>
												<td>Start Time:</td>
												<td><?php echo $eStime; ?></td>
											</tr>
											<tr>
												<td>Address:</td>
												<td>
												<?php
												echo $eVenueAdd . ",</br>";
												echo $eCity .  ",</br>";
												echo $eState .  " " . $ePostcode . ",</br>";
												echo $eCountry . "</br>";
												?>
												</td>
											</tr>
											<tr>
												<td>Phone:</td>
												<td>
												<?php
												echo $eVenuePhone;
												?>
												</td>
											</tr>
											<tr>
												<td>Venue:</td>
												<td>
												<?php
												echo $eVenueName;
												?>
												</td>
											</tr>
											<tr>
												<td>Yelp Rating:</td>
												<td>
												<?php
												echo "<img src = '". $eVenueYelpRating ."'>";
												?>
												</td>
											</tr>					  
										</tbody>
									</table>

									<div id="fb-root"></div>
									<script>
									(function(d, s, id) {
										var js, fjs = d.getElementsByTagName(s)[0];
										if (d.getElementById(id)) return;
										js = d.createElement(s); js.id = id;
										js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=661658400621739&version=v2.0";
										fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));
									</script>
									<a href=<?php echo "'".$eVenueYelp."'"; ?> class="btn btn-primary" target='_blank'>Yelp Reviews</a>
									<div class="fb-share-button" data-href=
									<?php echo "'" . $eventUrl . "'";
									?>
									data-layout="button_count"></div>
									</br>
									</br>
									<div id="googleMap" style="width:500px;height:350px;position:relative;float:left"></div>
									<div style="position:relative;float:right">
									<?php
									echo "<h4>Users Attending:</h4>";
									while($row = mysqli_fetch_array($users)){
										$pictureUrl = 'https://graph.facebook.com/' . $row["fid"] . '/picture';
										echo "<img src = ".$pictureUrl." style='margin-left:5px; margin-right:5px;'>";
									}
									?>
									</div>
								</br>
								</div>
							</div>
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
</html><!-- 
Cloud Computing Final Project - Event Recommendation System
Presents the information and links about the event and the venue, additionally, presents information from Yelp about the venue.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
session_start();

if (isset($_SESSION["fb_token"])) {
	require_once "database.php";
	require_once('lib/OAuth.php');
	$eventId = $_GET['id'];	
	if(isset($_REQUEST["going"])){
		if($_REQUEST["going"] == "Attend"){
			$insertSql = "insert into Going(eid, fid) VALUES ('" . $eventId . "','" .$_SESSION["fid"]. "')";
			$result = $conn->query($insertSql); 
		}
		else{
			$insertSql = "delete from Going where eid='" . $eventId . "' and fid='" .$_SESSION["fid"]. "'";
			$result = $conn->query($insertSql); 
		}
	}
	$sql = "select * from MergedEvents where id1 = '" . $eventId . "'"; 
	$result = $conn->query($sql);
	
	if(mysqli_num_rows($result) != 1) {
		header("location:welcome.php");
	}

	$event = mysqli_fetch_array($result);
	$eventDesc = trim($event["edesp"]);
	$title = trim($event["title"]);
	$eventUrl = trim($event["eurl"]);
	$eCity = trim($event["ecity"]);
	$eState = trim($event["estate"]);
	$eCountry = trim($event["ecounter"]);
	$ePostcode = trim($event["epostcode"]);
	$eVenueId = trim($event["evenueid"]);
	$eVenueName = trim($event["evenuename"]);
	$eVenueUrl = trim($event["evenueurl"]);
	$eLat = floatval(trim($event["elat"]));
	$eLng = floatval(trim($event["elng"]));
	$eStime = trim($event["estime"]);
	$coordinates =  $eLat . "," . $eLng;
	$eVenueAdd = trim($event["evenueadd"]);

	$CONSUMER_KEY = YELP_CONSUMER_KEY;
	$CONSUMER_SECRET = YELP_CONSUMER_SECRET;
	$TOKEN = YELP_TOKEN;
	$TOKEN_SECRET = YELP_TOKEN_SECRET;
	$API_HOST = 'api.yelp.com';
	$DEFAULT_TERM = $eVenueName;
	$DEFAULT_LOCATION = $eState . ' ' . $ePostcode;
	$SEARCH_LIMIT = 1;
	$SEARCH_PATH = '/v2/search/';
	$BUSINESS_PATH = '/v2/business/';

	function request($host, $path) {
		$unsigned_url = "http://" . $host . $path;
		$token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);
		$consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		$oauthrequest = OAuthRequest::from_consumer_and_token(
			$consumer, 
			$token, 
			'GET', 
			$unsigned_url
		);
		$oauthrequest->sign_request($signature_method, $consumer, $token);
		$signed_url = $oauthrequest->to_url();
		$ch = curl_init($signed_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);	
		return $data;
	}

	function search($term, $location) {
		$url_params = array();
		$url_params['term'] = $term ?: $GLOBALS['DEFAULT_TERM'];
		$url_params['location'] = $location?: $GLOBALS['DEFAULT_LOCATION'];
		$url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
		$search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params);	
		return request($GLOBALS['API_HOST'], $search_path);
	}

	function get_business($business_id) {
		$business_path = $GLOBALS['BUSINESS_PATH'] . $business_id;
		return request($GLOBALS['API_HOST'], $business_path);
	}

	function query_api($term, $location) {     
		$response = json_decode(search($term, $location));
		$business_id = $response->businesses[0]->id;
		return $response;
	}

	$longopts  = array(
		"term::",
		"location::",
	);
	$options = getopt("", $longopts);
	$term = $options['term'] ?: '';
	$location = $options['location'] ?: '';
	$response =  query_api($term, $location);
	$eVenueYelp = $response->businesses[0]->url;
	$eVenueYelpRating = $response->businesses[0]->rating_img_url;
	$eVenuePhone = $response->businesses[0]->display_phone;
	$eVenueImage = $response->businesses[0]->image_url;
	$goingSql = "select fid from Going where eid='" . $eventId . "' and fid='" .$_SESSION["fid"]. "'";
	$result = $conn->query($goingSql);
	if(mysqli_num_rows($result) != 1) {
		$buttonValue = false;
	}
	else {
		$buttonValue = true;
	}
	$usersAttendingSql = "select fid from Going where eid='" . $eventId . "'";
	$users=$conn->query($usersAttendingSql);
	mysqli_close($conn);
}
else {
	header("location:index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Event</title>
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
		<style type="text/css">
		html, body, #map-canvas {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		</style>
		<script src="http://maps.googleapis.com/maps/api/js"></script>
		<script>
		var myCenter=new google.maps.LatLng(
		<?php
		echo $coordinates;
		?>
		);
		function initialize() {
			var mapProp = {
				center: myCenter,
				zoom:5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
			var marker = new google.maps.Marker({
				position: myCenter,
				title:'Click to zoom'
			});
			marker.setMap(map);
			google.maps.event.addListener(marker,'click',function() {
				map.setZoom(9);
				map.setCenter(marker.getPosition());
			});
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		</script>
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
							<li><a href="logugoot.php ">Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">	  
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
					<div class="panel panel-info">
						<div class="panel-heading">
							</br>
							</br> 
							<h3 class="panel-title"><?php echo "<a href = '" . $eventUrl . "' target='_blank'>" . $title . "</a>"; ?></h3>
							<form method="post">
								<input type="submit" name="going" id="going" class ="btn btn-primary" value=
								<?php 
								if($buttonValue == false){
									echo "Attend";
								} 
								else{
									echo "Withdraw";
								}
								?>
								/>
							</form>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-3 col-lg-3 " align="center"><img src=
								<?php
								echo "'".$eVenueImage."'";
								?> 
								>
								</div>
								</br>
								<div class=" col-md-9 col-lg-9 ">
									<table class="table table-user-information">
										<tbody>
											<tr>
												<td>Description:</td>
												<td><?php echo $eventDesc; ?></td>
											</tr>
											<tr>
												<td>Start Time:</td>
												<td><?php echo $eStime; ?></td>
											</tr>
											<tr>
												<td>Address:</td>
												<td>
												<?php
												echo $eVenueAdd . ",</br>";
												echo $eCity .  ",</br>";
												echo $eState .  " " . $ePostcode . ",</br>";
												echo $eCountry . "</br>";
												?>
												</td>
											</tr>
											<tr>
												<td>Phone:</td>
												<td>
												<?php
												echo $eVenuePhone;
												?>
												</td>
											</tr>
											<tr>
												<td>Venue:</td>
												<td>
												<?php
												echo $eVenueName;
												?>
												</td>
											</tr>
											<tr>
												<td>Yelp Rating:</td>
												<td>
												<?php
												echo "<img src = '". $eVenueYelpRating ."'>";
												?>
												</td>
											</tr>					  
										</tbody>
									</table>

									<div id="fb-root"></div>
									<script>
									(function(d, s, id) {
										var js, fjs = d.getElementsByTagName(s)[0];
										if (d.getElementById(id)) return;
										js = d.createElement(s); js.id = id;
										js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=661658400621739&version=v2.0";
										fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));
									</script>
									<a href=<?php echo "'".$eVenueYelp."'"; ?> class="btn btn-primary" target='_blank'>Yelp Reviews</a>
									<div class="fb-share-button" data-href=
									<?php echo "'" . $eventUrl . "'";
									?>
									data-layout="button_count"></div>
									</br>
									</br>
									<div id="googleMap" style="width:500px;height:350px;position:relative;float:left"></div>
									<div style="position:relative;float:right">
									<?php
									echo "<h4>Users Attending:</h4>";
									while($row = mysqli_fetch_array($users)){
										$pictureUrl = 'https://graph.facebook.com/' . $row["fid"] . '/picture';
										echo "<img src = ".$pictureUrl." style='margin-left:5px; margin-right:5px;'>";
									}
									?>
									</div>
								</br>
								</div>
							</div>
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