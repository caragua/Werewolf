<?php
	
	require("ingame.functions.php");
	$ans = getRoomDayStatus(2);
	echo $ans[0] . $ans[1];
	
	playerStatusUpdate('room', 3, 2);
	
	
?>