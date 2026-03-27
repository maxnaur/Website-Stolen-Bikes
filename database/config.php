<?php
	$servername = "localhost";
	$username = "ENTER_DATABASE_USER_NAME_HERE";
	$password = "ENTER_DATABASE_PASSWORD_HERE";
	$dbname = "ENTER_DATABASE_NAME_HERE";

	$connection = new mysqli($servername, $username, $password, $dbname);

	if($connection->connect_error) {
		echo $connection->connect_error;
	}
?>