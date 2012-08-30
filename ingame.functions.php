<?php

	/*	References
	
		http://stackoverflow.com/questions/9836150/php-mysql-limit-query-result-by-time-difference
	
	*/

	/*	Notes
	
		gameCheckTime = it is used every time the game reaches it's check point (entering day/night/day-vote/night-vote)
		
		gameCurrentTime = for the current part of "day" inside the game (0/1/2/3 for day/day-vote/night/night-vote)
		
		delete from gamechat; update game set gameStatus = 0, gameDayCount = 0, gameCurrentTime = 0, gameCheckTime = '2012-01-01 00:00:00'; delete from player
		
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
	
	/* Retrives the Room's name
	function getRoomName($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from game where gameId = $roomId order by gameId desc limit 1";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = "";
		$name = "";
		$note = "";
			
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id = $dbResRows['gameID'];
				$name = $dbResRows['gameName'];
				$note = $dbResRows['gameNote'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "name"=>$name, "note"=>$note);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}
	}
	*/
	
	// Changes the player's ingame status
	// 0=Waiting 1=Ready 2=Alive 3=Dead
	function playerGameStatus($playerid, $state)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "update player set playerStatus = $state where playerId = $playerid";
		
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
	
	// Retrives the chat messages while the room is in "waiting: status
	function roomChatRequestWaiting($roomid, $lastmesgid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd = "select * from (select * from gamechat where gChatId > $lastmesgid and gameId = $roomid order by gChatId) as sub order by gChatId asc";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = array();
		$nick = array();
		$mesg = array();
		$type = array();
		$size = array();
		$date = array();
			
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id[] = $dbResRows['gChatId'];
				$nick[] = $dbResRows['playerNick'];
				$mesg[] = $dbResRows['gChatMessage'];
				$type[] = $dbResRows['gChatType'];
				$size[] = $dbResRows['gChatSize'];
				$date[] = $dbResRows['gChatDate'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "nick"=>$nick, "mesg"=>$mesg, "type"=>$type, "size"=>$size, "date"=>$date);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}
	}
	
	// Updates player's last access to be identified as "active"
	function playerActivePing($playerid, $gameid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$activePing = rand(0, 128);
		$dbCmd= "update player set playerActivePing = $activePing where playerId = $playerid and gameId = $gameid";
		
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
	
	// Returns players marked as "active" inside a gameroom
	function playerRequestWaiting($gameid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select playerId, userNick, userAvatar, playerStatus, time_to_sec(timediff(now(), playerLastAct)) as accessTime from player where gameId = $gameid and (time_to_sec(timediff(now(), playerLastAct)) < 3 or playerStatus = 2 or playerStatus = 3) order by playerId desc";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = array();
		$nick = array();
		$avatar = array();
		$status = array();
		$accessTime = array();
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id[] = $dbResRows['playerId'];
				$nick[] = $dbResRows['userNick'];
				$avatar[] = $dbResRows['userAvatar'];
				$status[] = $dbResRows['playerStatus'];
				$accessTime[] = $dbResRows['accessTime'];
			}
			$dbResult->close();
			$dbConn->close();
			
			$answer = array("id"=>$id, "nick"=>$nick, "avatar"=>$avatar, "status"=>$status, "accessTime"=>$accessTime);
			echo json_encode($answer);
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}		
	}
	
	// Returns players marked as "ready" inside a gameroom
	function playersReady($gameid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from player where gameId = $gameid and time_to_sec(timediff(now(), playerLastAct)) < 3";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = $dbResult->num_rows;
		
		$dbResult->close();
		$dbConn->close();
		
		return $answer;				
	}
	
	// Returns a user's information
	// In this case: userID and userNick
	function getUserInfo($userid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from user where userID = $userid order by userID desc limit 1";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = "";
		$nick = "";
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id = $dbResRows['userID'];
				$nick = $dbResRows['userNick'];
			}
			$dbResult->close();
			$dbConn->close();
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}		
		
		return $answer = array("id"=>$id, "nick"=>$nick);
	}
	
	// Creates a Player inside a game
	function playerJoinGame($roomid, $userid, $usernick, $useravatar)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "insert into player (gameId, userId, userNick, userAvatar) values ($roomid, $userid, '$usernick', '$useravatar')";
			
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
	
	function getPlayerInfo($roomid, $userid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from player where userId = $userid and gameId = $roomid order by playerId desc limit 1";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = "";
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer = $dbResRows['playerId'];
			}
			$dbResult->close();
			$dbConn->close();
			echo $answer;
			return true;
		}
		else
		{	$dbResult->close();
			$dbConn->close();
			return false;
		}		
	}
	
	function chatSendWaiting($playerid, $playernick, $message, $gameid, $fontsize)
	{	$currentTimeSet = getRoomDayStatus($gameid);
	
		/* check the current game status.
		if status = waiting or (active&day), chat type = 0
		if status = active and night, chat type is set to the player's character specific channel number.
		
		*/
		
		global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$curDay = $currentTimeSet[0];
		$curTime = $currentTimeSet[1];
		
		$message = filterChars($message);
		$message = str_replace("AAAA-NNNN-DDDD", "&",$message);
		$message = str_replace("PPPP-LLLL-UUUU-SSSS", "+",$message);
		
		$dbCmd= "insert into gamechat (gameId, playerId, playerNick, gChatMessage, gChatDay, gChatTime, gChatSize) values ($gameid, $playerid, '$playernick', '$message', $curDay, $curTime, $fontsize)";
		
		// For use with the above method
		//$dbCmd= "insert into gamechat (gameId, playerId, playerNick, gChatMessage, gChatDay, gChatTime, gChatType) values ($gameid, $playerid, '$playernick', '$message', $curDay, $curTime, $type)";
		
		if ($dbConn->query($dbCmd) === TRUE)
		{	$dbConn->close();
		}
		else
		{	printf($dbConn->connect_error);
			$dbConn->close();
		}
	}
	
	// Eliminates players that have lefted a room before it's started.
	function playerLeftRoom($gameid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		//$dbCmd= "update player set playerActivePing = $activePing where playerId = $playerid and gameId = $gameid";
		$dbCmd= "delete from player where gameId = $gameid and time_to_sec(timediff(now(), playerLastAct)) > 3 and (playerStatus = 0 or playerStatus = 1)";
		
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
	
	// Start game when 8 players or more are in ready state
	function initGame($roomid)
	{	$playerCant = playersReady($roomid);
	
		global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from player where gameId = $roomid and (time_to_sec(timediff(now(), playerLastAct)) < 3) and playerStatus = 1";
		$dbResult = $dbConn->query($dbCmd);
		
		if($dbResult->num_rows >= $playerCant)
		{	$dbResult->close();
			$dbConn->close();
			
			// Stop the chat while the system processes
			sysCommand($roomid, 'stopchat');
			
			// Start to execute things
			sysCommand($roomid, 'processing');
			
			// Mark all active players with status = 2, alive.
			playerStatusUpdate('room', 2, $roomid);
			/*
				Assign players a character depending on the quantity of players.
			*/
			roomStatusUpdate($roomid, 1);
			updateGameHoster($roomid);
			
			// Finished the processes and start the game
			roomProgressUpdateDay($roomid, 1, 1);
			sysCommand($roomid, 'initgame');
			
			// Start client side's chat input
			sysCommand($roomid, 'startchat');
		}
		else
		{	$dbResult->close();
			$dbConn->close();
		}		
	}
	
	// Returns the players' id who are ready to be assigned as the hoster
	function playerReadyForHost($gameid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select * from player where gameId = $gameid and time_to_sec(timediff(now(), playerLastAct)) < 2 order by playerId";
		$dbResult = $dbConn->query($dbCmd);
		
		$id = array();
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$id[] = $dbResRows['playerId'];
			}
			$dbResult->close();
			$dbConn->close();
			
			return $id;
		}
		else
		{	$dbResult->close();
			$dbConn->close();
			return "";
		}		
	}
	
	// Changes the player's ingame status of a game
	// 0=Waiting 1=Ready 2=Alive 3=Dead
	function playerStatusUpdate($type, $state, $id)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd = "";
		
		if($type == 'room')
		{	$dbCmd= "update player set playerStatus = $state where gameId = $id";
		}
		elseif($type == 'player')
		{	$dbCmd= "update player set playerStatus = $state where playerId = $id";
		}
		
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
	
	// Send system commands for use within the game.
	function sysCommand($gameid, $message)
	{	$currentTimeSet = getRoomDayStatus($gameid);
	
		global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$curDay = $currentTimeSet[0];
		$curTime = $currentTimeSet[1];
		
		$dbCmd= "insert into gamechat (gameId, gChatMessage, gChatDay, gChatTime, gChatType) values ($gameid, '$message', $curDay, $curTime, 99)";
		
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
	
	// Update a Rooms Status
	function roomStatusUpdate($roomid, $val)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "update game set gameStatus = $val where gameId = $roomid";
		
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
	
	// Update a Room's Check Time
	function roomCheckTimeUpdate($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "update game set gameCheckTime = NOW() where gameId = $roomid";
		
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
	
	// Update a Rooms Current Day Count
	// $day -> 1 for a day passed
	// $time -> 1 for a day/night change
	function roomProgressUpdateDay($roomid, $day, $time)
	{	$currentTimeSet = getRoomDayStatus($roomid);
		
		global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$newDay = $currentTimeSet[0] + $day;
		$newTime = 0;
		
		if($currentTimeSet[1] == 3 && $time == 1)
		{	$newDay = $newDay + 1;
			$newTime = 0;
		}
		else
		{	$newTime = $currentTimeSet[1] + $time;
		}
		
		$dbCmd= "update game set gameDayCount = $newDay, gameCurrentTime = $newTime where gameId = $roomid";
		
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
	
	// Returns room's current day and time
	function getRoomDayStatus($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select gameDayCount, gameCurrentTime from game where gameId = $roomid";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = array(0, 0);
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer[0] = $dbResRows['gameDayCount'];
				$answer[1] = $dbResRows['gameCurrentTime'];
			}
			$dbResult->close();
			$dbConn->close();
			return $answer;
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			return $answer;
		}		
	}
	
	// Returns time left for the next stage.
	function getRoomTimeLeft($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		//$dbCmd= "select gameDayCount, gameCurrentTime from game where gameId = $roomid";
		$dbCmd= "select gameName, time_to_sec(timediff(now(), gameCheckTime)) as passed, gameCurrentTime, gameDayDuration, gameNightDuration, gameActionDuration, gameHoster from game where gameId = $roomid";
		$dbResult = $dbConn->query($dbCmd);
		
		$gameName = "";
		$timePassed = 0;
		$timeRemaining = 0;
		$currentTime = 0;
		$dayDuration = 0;
		$nightDuration = 0;
		$actionDuration = 0;
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$gameName = $dbResRows['gameName'] . " [Host:" . $dbResRows['gameHoster'] . "] ";
				//$gameName = $dbResRows['gameName'];
				$timePassed = $dbResRows['passed'];
				$currentTime = $dbResRows['gameCurrentTime'];
				$dayDuration = $dbResRows['gameDayDuration'];
				$nightDuration = $dbResRows['gameNightDuration'];
				$actionDuration = $dbResRows['gameActionDuration'];
			}
			$dbResult->close();
			$dbConn->close();
			
			if($currentTime == 0)
			{	$timeRemaining = ($dayDuration * 60) - $timePassed;
			}
			if($currentTime == 2)
			{	$timeRemaining = ($nightDuration * 60) - $timePassed;
			}
			if($currentTime == 1 || $currentTime == 3)
			{	$timeRemaining = ($actionDuration * 60) - $timePassed;
			}
			
			$answer = array("name"=>$gameName, "stage"=>$currentTime, "passed"=>$timePassed, "remaining"=>$timeRemaining);
			
			echo json_encode($answer);
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			echo "";
		}		
	}
	
	// Return a room's status
	// 0 for waiting, 1 for active, 2 for finished, 3 for abandoned
	function getRoomStatus($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select gameStatus from game where gameId = $roomid";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = "";
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer = $dbResRows['gameStatus'];
			}
			$dbResult->close();
			$dbConn->close();
			return $answer;
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			return $answer;
		}		
	}
	
	// Return a room's process status
	function getRoomProcessStatus($roomid, $type)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select $type from game where gameId = $roomid";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = "";
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer = $dbResRows[$type];
			}
			$dbResult->close();
			$dbConn->close();
			return $answer;
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			return $answer;
		}		
	}
	
	// Update a Room's Process Status
	function updateRoomProcessStatus($roomid, $type, $status)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "update game set $type = $status where gameId = $roomid";
		
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
	
	// Return a room's process status
	function getRoomHoster($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select gameHoster from game where gameId = $roomid";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = "";
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer = $dbResRows['gameHoster'];
			}
			$dbResult->close();
			$dbConn->close();
			return $answer;
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			return $answer;
		}		
	}
	
	// Update a Room's Process Status
	function updateGameHoster($roomid)
	{	$listPlayers = playerReadyForHost($roomid);
		$aplayer = $listPlayers[0];
		
		if(getRoomHoster($roomid) != $aplayer)
		{	global $dbServer, $dbAcc, $dbPass, $dbName;	
			$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
			$dbConn->query("set names utf8");
			if ($dbConn->connect_errno)
			{	printf("Connect failed: %s\n", $dbConn->connect_error);
				exit();
			}
			
			$dbCmd= "update game set gameHoster = $aplayer where gameId = $roomid";
			
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
		else
		{	return true;
		}
	}
	
	function getGameStatusRelated($roomid)
	{	global $dbServer, $dbAcc, $dbPass, $dbName;	
		$dbConn = new mysqli($dbServer, $dbAcc, $dbPass, $dbName);
		$dbConn->query("set names utf8");
		if ($dbConn->connect_errno)
		{	printf("Connect failed: %s\n", $dbConn->connect_error);
			exit();
		}
		
		$dbCmd= "select time_to_sec(timediff(now(), gameCheckTime)) as timeFlow, gameStatus, gameActionProcess, gameDayEventProcess, gameNightEventProcess, gameDayCount, gameCurrentTime, gameDayDuration, gameNightDuration, gameActionDuration from game where gameId = $roomid limit 1";
		$dbResult = $dbConn->query($dbCmd);
		
		$answer = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		
		if($dbResult->num_rows > 0)
		{	while($dbResRows = $dbResult->fetch_assoc())
			{	$answer[0] = $dbResRows['timeFlow'];
				$answer[1] = $dbResRows['gameStatus'];
				$answer[2] = $dbResRows['gameActionProcess'];
				$answer[3] = $dbResRows['gameDayEventProcess'];
				$answer[4] = $dbResRows['gameNightEventProcess'];
				$answer[5] = $dbResRows['gameDayCount'];
				$answer[6] = $dbResRows['gameCurrentTime'];
				$answer[7] = $dbResRows['gameDayDuration'];
				$answer[8] = $dbResRows['gameNightDuration'];
				$answer[9] = $dbResRows['gameActionDuration'];
			}
			$dbResult->close();
			$dbConn->close();
			return $answer;
		}
		else
		{	//$dbResult->close();
			$dbConn->close();
			return $answer;
		}
	}
	
	// Get a game's current status, check it and send it to different "processes" (dayEvent, NightEvent, etc)
	function gameStatusProcess($roomid)
	{	$gameInfo = getGameStatusRelated($roomid);
		
		$timeFlow = $gameInfo[0];
		$currentTime = $gameInfo[6];
		$dayDuration = $gameInfo[7];
		$nightDuration = $gameInfo[8];
		$actionDuration = $gameInfo[9];
		
		//	roomProgressUpdateDay($roomid, $day, $time)
		//	sysCommand($gameid, $message)
		
		//	getRoomProcessStatus($roomid, $type)
		//	updateRoomProcessStatus($roomid, $type, $status)
		
		if(getRoomProcessStatus($roomid, 'gameDayEventProcess') == 0 && $currentTime == 0 && $timeFlow >= ($dayDuration * 60))
		{	// Mark this process as active so that other's won't execute this
			updateRoomProcessStatus($roomid, 'gameDayEventProcess', 1);
			// Stop the chat while the system processes
			sysCommand($roomid, 'stopchat');
			
			// Start to execute things
			sysCommand($roomid, 'processing');
			
			//	Processes:
			//
			
			
			
			roomProgressUpdateDay($roomid, 0, 1);
			roomCheckTimeUpdate($roomid);
			// Mark this status as processed
			updateRoomProcessStatus($roomid, 'gameDayEventProcess', 0);					
			sysCommand($roomid, 'dayvote');
			
			// Start client side's chat input
			//sysCommand($roomid, 'startchat');	
			
		}
		elseif(getRoomProcessStatus($roomid, 'gameActionProcess') == 0 && $currentTime == 1 && $timeFlow >= ($actionDuration * 60))
		{	// Mark this process as active so that other's won't execute this
			updateRoomProcessStatus($roomid, 'gameActionProcess', 1);
			// Stop the chat while the system processes
			//sysCommand($roomid, 'stopchat');
			
			// Start to execute things
			sysCommand($roomid, 'processing');
			
			//	Processes:
			//	Vote Process
			//	Result Process
			//	Game Over Process					
			
			roomProgressUpdateDay($roomid, 0, 1);
			roomCheckTimeUpdate($roomid);					
			updateRoomProcessStatus($roomid, 'gameActionProcess', 0);		
			sysCommand($roomid, 'night');
			
			//	Announce Dead Person	
			
			// Start client side's chat input
			sysCommand($roomid, 'startchat');	
			
		}
		elseif(getRoomProcessStatus($roomid, 'gameNightEventProcess') == 0 && $currentTime == 2 && $timeFlow >= ($nightDuration * 60))
		{	// Mark this process as active so that other's won't execute this
			updateRoomProcessStatus($roomid, 'gameNightEventProcess', 1);
			// Stop the chat while the system processes
			sysCommand($roomid, 'stopchat');
			
			// Start to execute things
			sysCommand($roomid, 'processing');
			
			//	Processes:
			//	
			
			
			roomProgressUpdateDay($roomid, 0, 1);
			roomCheckTimeUpdate($roomid);
			updateRoomProcessStatus($roomid, 'gameNightEventProcess', 0);
			sysCommand($roomid, 'nightvote');
			
			// Start client side's chat input
			//sysCommand($roomid, 'startchat');		
			
		}
		elseif(getRoomProcessStatus($roomid, 'gameActionProcess') == 0 && $currentTime == 3 && $timeFlow >= ($actionDuration * 60))
		{	// Mark this process as active so that other's won't execute this
			updateRoomProcessStatus($roomid, 'gameActionProcess', 1);
			// Stop the chat while the system processes
			//sysCommand($roomid, 'stopchat');
			
			// Start to execute things
			sysCommand($roomid, 'processing');
			
			//	Processes:
			//	Bite Process
			//	Divine Prcesses
			//	Result Process
			//	Game Over Process					
			
			roomProgressUpdateDay($roomid, 0, 1);
			roomCheckTimeUpdate($roomid);
			updateRoomProcessStatus($roomid, 'gameActionProcess', 0);	
			sysCommand($roomid, 'day');
			
			//	Announcements:
			//	Divine Result
			//	Dead Person
			
			// Start client side's chat input
			sysCommand($roomid, 'startchat');
			
		}
	}
	
	
	
?>