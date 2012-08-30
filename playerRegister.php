<?php
	
	require("functions.php");
	if(playerRegister($_POST['email'], $_POST['pass'], $_POST['nick']))
	{	playerLogin($_POST['email'], $_POST['pass']);
	}
	
?>