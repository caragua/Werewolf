<?php

	require("ingame.functions.php");
	
	playerRequestWaiting($_POST['gameid']);
	playerLeftRoom($_POST['gameid']);
	
?>