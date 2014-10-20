<?php
/** 
 * Designed by DDR Software.
 * This file setups up the database connection
 * INSTRUCTIONS:
 * RUN THIS IN TERMINAL (INSIDE BURGERBAR REPO) TO IGNORE THIS FILE
 * git update-index --assume-unchanged ./api/config.php
 * THEN, FEEL FREE TO EDIT INFO TO MATCH YOUR SQL INFO
 */

require '../Slim/Slim.php';//ROUTE TO YOUR OWN SLIM FOLDER HERE

function getConnection() 
	{
		$dbhost="127.0.0.1";
		$dbuser="root";
		$dbpass="";
		$dbname="BurgerBar";
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}

?>