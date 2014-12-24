<!-- 
Cloud Computing Final Project - Event Recommendation System
The database connection used throughout the web application.
Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
-->
<?php
$dburl = DATABASE_URL;
$dbuser = DATABASE_USERNAME;
$dbpassword = DATABASE_PASSWORD;
$dbname = DATABASE_NAME;
$conn = mysqli_connect($dburl,$dbuser,$dbpassword,$dbname);
?>