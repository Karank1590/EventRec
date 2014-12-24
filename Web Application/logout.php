<!-- 
Cloud Computing Final Project - Event Recommendation System
Destroys the session and logs out the user from the website.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php 
unset($_SESSION["user"]);
unset($_SESSION["fid"]);
unset($_SESSION["fb_token"]);
unset($_SESSION["userRealName"]);
unset($_SESSION["userProfilePicture"]);
session_destroy();
header("location:index.php");
?>