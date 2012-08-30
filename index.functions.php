<?php

	/*	References
	
		http://stackoverflow.com/questions/9836150/php-mysql-limit-query-result-by-time-difference
	
	*/

	// server's ip
	$dbServer = "localhost";
		
	// account for the database
	$dbAcc = "root";
	
	// password for that account
	$dbPass = "celerond352";
	
	// db used for this system
	$dbName = "werewolf";
		
	
	function filterChars($input)
	{	return str_replace("\n", "<br>",  htmlspecialchars($input));
		//return mysql_real_escape_string(str_replace("\n", "<br>",  htmlspecialchars(str_replace("'", "’", (str_replace("+", "†", $input))))));
	}
	
	function playerLogin($email, $pass)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$email = filterChars($email);
		$pass = filterChars($pass);
		
		$dbCmd= "select * from user where userEmail = '$email' and userPass = '$pass' order by userID desc limit 1";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = "";
		$nickname = "";
		$avatar = "";
			
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id = $dbResRows['userID'];
				$nickname = $dbResRows['userNick'];
				$avatar = $dbResRows['userAvatar'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "nickname"=>$nickname, "avatar"=>$avatar);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}
	}
	
	function playerWaitChatSend($playerid, $playernick, $message)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$message = filterChars($message);
		
		$dbCmd= "insert into waitchat (userId, userNick, wChatMessage) values ($playerid, '$playernick', '$message')";
		
		if ($dbConn->query($dbCmd) === TRUE)
		{	$dbConn->close();
		}
		else
		{	printf($dbConn->connect_error);
			$dbConn->close();
		}
	}
	
	function playerWaitChatRequest($lastmesgid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd = "select * from (select * from waitchat where wChatId > $lastmesgid order by wChatId desc limit 30) as sub order by wChatId asc";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = array();
		$nick = array();
		$mesg = array();
		$date = array();
			
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id[] = $dbResRows['wChatId'];
				$nick[] = $dbResRows['userNick'];
				$mesg[] = $dbResRows['wChatMessage'];
				$date[] = $dbResRows['wChatDate'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "nick"=>$nick, "mesg"=>$mesg, "date"=>$date);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}
	}
	
	function playerWaitChatPing($playerid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$activePing = rand(0, 128);
		$dbCmd= "update user set userActPing = $activePing where userID = $playerid";
		
		if ($dbConn->query($dbCmd) === TRUE)
		{	$dbConn->close();
		}
		else
		{	printf($dbConn->connect_error);
			$dbConn->close();
		}
	}
	
	function PlayersWaitingActive()
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from user where time_to_sec(timediff(now(), userLastAct)) < 5 order by userID";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = array();
		$nick = array();
		$avatar = array();
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id[] = $dbResRows['userID'];
				$nick[] = $dbResRows['userNick'];
				$avatar[] = $dbResRows['userAvatar'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "nick"=>$nick, "avatar"=>$avatar);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}		
	}
	
	function playerRegister($email, $pass, $nick)
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
			return true;
		}
		else
		{	printf($dbConn->connect_error);
			$dbConn->close();
			return false;
		}
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
?>