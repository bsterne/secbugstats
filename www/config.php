<?php
$dbHost = "";
$dbUser = "";
$dbPass = "";
$dbDB = "";
$link = mysql_connect($dbHost, $dbUser, $dbPass)
  or die("Could not connect to database host.");
mysql_select_db($dbDB) or die("Could not select database");
?>
