<?php

	function playerRegister1($email, $pass, $nick)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$email = filterChars($email);
		$pass = filterChars($pass);
		$nick = filterChars($nick);
		
		$dbCmd= "insert into user (userNick, userEmail, userPass) values ('$nick', '$email', '$pass')";
			
		if ($dbConn->query($dbCmd) === TRUE)
		{	$dbConn->close();
		}
		else
		{	printf($dbConn->connect_error);
			$dbConn->close();
		}
	}
	
?>
