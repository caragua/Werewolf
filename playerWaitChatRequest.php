<?php
	
	require("functions.php");
	
	if(!($_POST['playerid'] == "" || $_POST['playerid'] == null))
	{	playerWaitChatPing($_POST['playerid']);
	}
	playerWaitChatRequest($_POST['lastid']);
	
?>