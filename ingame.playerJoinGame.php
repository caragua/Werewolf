<?php

	require("ingame.functions.php");
	
	if(playerJoinGame($_POST['gameid'], $_POST['userid'], $_POST['usernick'], $_POST['useravatar']))
	{	getPlayerInfo($_POST['gameid'], $_POST['userid']);
	}

?>