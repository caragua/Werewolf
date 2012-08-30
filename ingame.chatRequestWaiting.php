<?php

	require("ingame.functions.php");
	
	roomChatRequestWaiting($_POST['gameid'], $_POST['lastid']);
	playerActivePing($_POST['playerid'], $_POST['gameid']);
	
	$gameStat = getRoomStatus($_POST['gameid']);
	
	// Check the game's status (as waiting, active, finished or abandoned)
	if($gameStat == 0)
	{	// If the room's in "waiting" state
	
		// Try to start the game
		updateGameHoster($_POST['gameid']);
		if(getRoomHoster($_POST['gameid']) == $_POST['playerid'])
		{	initGame($_POST['gameid']);
		}
	}
	elseif($gameStat == 1)
	{	// If the room's in "active" state
		// if player is the host then
		
		updateGameHoster($_POST['gameid']);
		
		if(getRoomHoster($_POST['gameid']) == $_POST['playerid'])
		{	gameStatusProcess($_POST['gameid']);
		}
	}
	elseif($gameStat == 2)
	{	// If the room's in "finished" state
	}
	elseif($gameStat == 3)
	{	// If the room's in "abandoned" state
	}
	
	
	
	
	
	
	// Test scripts
	//
	
	
?>