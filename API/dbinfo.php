<?php 
    $host = "localhost";
    $database = "ninjavan";
    $user = "root";
    $pass = "Naing110!";
    $conn = new mysqli($host, $user, $pass, $database);
		if ($conn->connect_error)
			die('Fail connection' . $conn->connect_error);
	  
?>