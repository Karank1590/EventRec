<!-- 
Cloud Computing Final Project - Event Recommendation System
Collects information(books, events, games, interests, liked pages, movies & music) about the user from Facebook. 
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<html>
	<head>
		<meta charset="utf-8">
		<title>Collect Interests</title>
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
					<a class="brand">EventRec</a>
					<div class="nav-collapse collapse"></div>
				</div>
			</div>
		</div>

		<div class="container">
			<h1>Collecting Interests from Facebook</h1>
			<div id="progress" style="width:1000px;border:1px solid #ccc;"></div>
			<div id="information" style="width"></div>
		</div>	
 		<?php
 		ini_set('max_execution_time', 600);
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
			
		if (isset($_SESSION["fb_token"])) {
			function progBar($i, $total){
				$percent = intval($i/$total * 100)."%";    
				echo '<script language="javascript">
				document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
				document.getElementById("information").innerHTML="'.$i.' category/categories processed.";
				</script>';
				
				echo str_repeat(' ',1024*64);
				flush();
			}
			$total = 7;
			progBar(0, $total);
			
			require_once "database.php";
			
			FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);
			$session = new FacebookSession( $_SESSION["fb_token"]);
			$fid = $_SESSION["fid"];
			$Eventrequest = new FacebookRequest( $session, 'GET', '/me/events' );
			$Eventresponse = $Eventrequest->execute();
			$EventgraphObject = $Eventresponse->getGraphObject();
			$EventDataGraph = $EventgraphObject->getProperty('data');
			$insertsql = "";
			if($EventDataGraph != NULL){
				$EventDataArray =  $EventDataGraph->asArray();
				for($i = 0; $i<count($EventDataArray) ; $i++) {
					$id = $EventDataArray[$i]->id;
					$name = $EventDataArray[$i]->name;
					$rsvp_status = $EventDataArray[$i]->rsvp_status;
					$location = $EventDataArray[$i]->location;
					$start_time = $EventDataArray[$i]->start_time;
					$insertsql = "INSERT ignore INTO Events(fid, eid, elocation, ename, estart_time, ersvp) VALUES (?,?,?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssssss", $fid, $id, $location, $name, $start_time, $rsvp_status);
					$stmt->execute();
					$stmt->close();
				}
				$pagingDataGraph = $EventgraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();
				
				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$EventDataGraph = $fb_obj->data;
					$EventDataArray =  $EventDataGraph;
					
					for($i = 0; $i<count($EventDataArray) ; $i++) {
						$id = $EventDataArray[$i]->id;
						$name = $EventDataArray[$i]->name;
						$rsvp_status = $EventDataArray[$i]->rsvp_status;
						$location = $EventDataArray[$i]->location;
						$start_time = $EventDataArray[$i]->start_time;
						$insertsql = "INSERT ignore INTO Events(fid, eid, elocation, ename, estart_time, ersvp) VALUES (?,?,?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssssss", $fid, $id, $location, $name, $start_time, $rsvp_status);
						$stmt->execute();
						$stmt->close();
					}
					
					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(1, $total);


			$likerequest = new FacebookRequest( $session, 'GET', '/me/likes' );
			$likeresponse = $likerequest->execute();
			$likegraphObject = $likeresponse->getGraphObject();
			$likeDataGraph = $likegraphObject->getProperty('data');
			$insertsql = "";

			if($likeDataGraph != NULL){
				$likeDataArray =  $likeDataGraph->asArray();

				for($i = 0; $i<count($likeDataArray) ; $i++) {
					$category = $likeDataArray[$i]->category;
					$id = $likeDataArray[$i]->id;
					$name = $likeDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO PageLike(fid, pid, pcategory, pname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}

				$pagingDataGraph = $likegraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();

				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$likeDataGraph = $fb_obj->data;
					$likeDataArray =  $likeDataGraph;

					for($i = 0; $i<count($likeDataArray) ; $i++) {
						$category = $likeDataArray[$i]->category;
						$id = $likeDataArray[$i]->id;
						$name = $likeDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO PageLike(fid, pid, pcategory, pname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}

					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(2, $total);


			$interestrequest = new FacebookRequest( $session, 'GET', '/me/interests' );
			$interestresponse = $interestrequest->execute();
			$interestgraphObject = $interestresponse->getGraphObject();
			$interestDataGraph = $interestgraphObject->getProperty('data');
			$insertsql = "";
			if($interestDataGraph != NULL){
				$interestDataArray =  $interestDataGraph->asArray();

				for($i = 0; $i<count($interestDataArray) ; $i++) {
					$category = $interestDataArray[$i]->category;
					$id = $interestDataArray[$i]->id;
					$name = $interestDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO Interests(fid, iid, icategory, iname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}

				$pagingDataGraph = $interestgraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();

				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);						
					$interestDataGraph = $fb_obj->data;
					$interestDataArray =  $interestDataGraph;

					for($i = 0; $i<count($interestDataArray) ; $i++) {
						$category = $interestDataArray[$i]->category;
						$id = $interestDataArray[$i]->id;
						$name = $interestDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO Interests(fid, iid, icategory, iname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}

					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(3, $total);


			$Bookrequest = new FacebookRequest( $session, 'GET', '/me/books' );
			$Bookresponse = $Bookrequest->execute();
			$BookgraphObject = $Bookresponse->getGraphObject();
			$BookDataGraph = $BookgraphObject->getProperty('data');
			$insertsql = "";
			if($BookDataGraph != NULL) {
				$BookDataArray =  $BookDataGraph->asArray();
				for($i = 0; $i<count($BookDataArray) ; $i++) {
					$category = $BookDataArray[$i]->category;
					$id = $BookDataArray[$i]->id;
					$name = $BookDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO Books(fid, bid, bcategory, bname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}
				$pagingDataGraph = $BookgraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();

				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$BookDataGraph = $fb_obj->data;
					$BookDataArray =  $BookDataGraph;
					for($i = 0; $i<count($BookDataArray) ; $i++) {
						$category = $BookDataArray[$i]->category;
						$id = $BookDataArray[$i]->id;
						$name = $BookDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO Books(fid, bid, bcategory, bname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}
					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(4, $total);


			$Gamerequest = new FacebookRequest( $session, 'GET', '/me/games' );
			$Gameresponse = $Gamerequest->execute();
			$GamegraphObject = $Gameresponse->getGraphObject();
			$GameDataGraph = $GamegraphObject->getProperty('data');
			$insertsql = "";
			if($GameDataGraph != NULL) {
				$GameDataArray =  $GameDataGraph->asArray();
				for($i = 0; $i<count($GameDataArray) ; $i++) {
					$category = $GameDataArray[$i]->category;
					$id = $GameDataArray[$i]->id;
					$name = $GameDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO Games(fid, gid, gcategory, gname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}
				$pagingDataGraph = $GamegraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();

				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$GameDataGraph = $fb_obj->data;
					$GameDataArray =  $GameDataGraph;
					for($i = 0; $i<count($GameDataArray) ; $i++) {
						$category = $GameDataArray[$i]->category;
						$id = $GameDataArray[$i]->id;
						$name = $GameDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO Games(fid, gid, gcategory, gname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}
					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(5, $total);


			$Movierequest = new FacebookRequest( $session, 'GET', '/me/movies' );
			$Movieresponse = $Movierequest->execute();
			$MoviegraphObject = $Movieresponse->getGraphObject();
			$MovieDataGraph = $MoviegraphObject->getProperty('data');
			$insertsql = "";
			if($MovieDataGraph != NULL) {
				$MovieDataArray =  $MovieDataGraph->asArray();
				for($i = 0; $i<count($MovieDataArray) ; $i++) {
					$category = $MovieDataArray[$i]->category;
					$id = $MovieDataArray[$i]->id;
					$name = $MovieDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO Movies(fid, moid, mocategory, moname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}
				$pagingDataGraph = $MoviegraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();
				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$MovieDataGraph = $fb_obj->data;
					$MovieDataArray =  $MovieDataGraph;
					for($i = 0; $i<count($MovieDataArray) ; $i++) {
						$category = $MovieDataArray[$i]->category;
						$id = $MovieDataArray[$i]->id;
						$name = $MovieDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO Movies(fid, moid, mocategory, moname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}
					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(6, $total);


			$Musicrequest = new FacebookRequest( $session, 'GET', '/me/music' );
			$Musicresponse = $Musicrequest->execute();
			$MusicgraphObject = $Musicresponse->getGraphObject();
			$MusicDataGraph = $MusicgraphObject->getProperty('data');	
			$insertsql = "";
			if($MusicDataGraph != NULL){
				$MusicDataArray =  $MusicDataGraph->asArray();
				for($i = 0; $i<count($MusicDataArray) ; $i++) {
					$category = $MusicDataArray[$i]->category;
					$id = $MusicDataArray[$i]->id;
					$name = $MusicDataArray[$i]->name;
					$insertsql = "INSERT ignore INTO Music(fid, muid, mucategory, muname) VALUES (?,?,?,?);";
					$stmt = mysqli_prepare($conn, $insertsql);
					$stmt->bind_param("ssss", $fid, $id, $category, $name);
					$stmt->execute();
					$stmt->close();
				}
				$pagingDataGraph = $MusicgraphObject->getProperty('paging');
				$pagingDataArray = $pagingDataGraph->asArray();
				while(array_key_exists('next', $pagingDataArray)) {
					$json_obj=file_get_contents($pagingDataArray['next']);
					$fb_obj=json_decode($json_obj);
					$MusicDataGraph = $fb_obj->data;
					$MusicDataArray =  $MusicDataGraph;
					for($i = 0; $i<count($MusicDataArray) ; $i++) {
						$category = $MusicDataArray[$i]->category;
						$id = $MusicDataArray[$i]->id;
						$name = $MusicDataArray[$i]->name;
						$insertsql = "INSERT ignore INTO Music(fid, muid, mucategory, muname) VALUES (?,?,?,?);";
						$stmt = mysqli_prepare($conn, $insertsql);
						$stmt->bind_param("ssss", $fid, $id, $category, $name);
						$stmt->execute();
						$stmt->close();
					}
					if(property_exists($fb_obj, "paging")==1) {
						$pagingDataGraph = $fb_obj->paging;
						$pagingDataArray = array();
						foreach ($pagingDataGraph as $key => $value) {
							$pagingDataArray[$key] = $value;
						}
					}
					else {
						break;
					}
				}
			}
			progBar(7, $total);
			
			mysqli_close($conn);
			echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
			echo '<meta http-equiv="Refresh" content="1; /welcome.php" />';
		}
		else {
			header("location:index.php");
		}
		?>
	</body>
</html>