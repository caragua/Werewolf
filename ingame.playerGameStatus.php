<?php

	require("ingame.functions.php");
	
	if(playerGameStatus($_POST['pid'], $_POST['state']) == true)
	{	echo "OK";
	}
	
?>