<?php
	//setcookie("wlfroom", $_POST['roomid'], time()+36000);
	setcookie("wlfroom", 2, time()+36000);
?>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

<LINK href="ingame.css" rel="stylesheet" type="text/css" media="screen">

<div class="spacer1">
</div>

<div id="left">

	<div id="PlayerList">
		<div id="PlayerListTitleContainer">
			<div id="PlayerListTitleContent">
				村民列表
			</div>
			<div class="footer"></div>
		</div>
		<div id="PlayerListContainer">
			<div id="PlayerListContent">
			</div>
			<div class="footer"></div>			
		</div>
	</div>
	
</div>

<div class="spacer1">
</div>

<div id="center">

	<div id="Chat">
		<div id="ChatTitleContainer">
			<div id="ChatTitleContent">
				村莊名稱
			</div>
		</div>
		<div id="ChatSendContainer">
			<div id="ChatSendContent">
				<form id='imsend' action='#' onsubmit='return false;'>
					<div style="float: left; width: 85%;">
						<textarea type='text' name='message' id='message' style='width: 100%; height: 100%'></textarea>
					</div>
					<div style="width: 15%; float: left;">
						<div class="ChatSendOptions" id="waitingChat">
							<button style="width: 100%; height: 100%" onclick='if(!(message.value == "")){chatSendWaiting(0);}'>普通</button>
						</div>
						<div class="ChatSendOptions" id="ingameChat">
							<button class="ChatSendOptionsContent" onclick='if(!(message.value == "")){chatSendWaiting(0);}'>普通</button>
							<button class="ChatSendOptionsContent" onclick='if(!(message.value == "")){chatSendWaiting(1);}'>小聲</button>
							<button class="ChatSendOptionsContent" onclick='if(!(message.value == "")){chatSendWaiting(2);}'>強勢</button>
							<button class="ChatSendOptionsContent" onclick='if(!(message.value == "")){sendDeadMessage();}'>遺言</button>
						</div>
					</div>
					<input type='hidden' name='lastid' value=0 />
					<input type='hidden' name='daynumber' value=0 />
					<input type='hidden' name='daytime' value=0 />
				</form>
			</div>
			<div class="footer"></div>
		</div>
		<div id="ChatContainer">
			<div id="ChatContent">			
				<div class="footer"></div>
			</div>
		</div>
	</div>
	
	<div class="footer">
	</div>

</div>

<div class="spacer1">
</div>

<div id="right">

	<div id="VillageInfo">
		<div id="VillageInfoTitleContainer">
			<div id="VillageInfoTitleContent">
				村莊詳細
			</div>
		</div>
		<div id="VillageInfoContainer">
			<div id="VillageInfoContent">
			</div>
		</div>
	</div>
	
	<div class="spacer2"></div>
	
	<div id="WaitOptions" class="Options">
		<div class="OptionsTitleContainer">
			<div class="OptionsTitleContent">
				村莊選項
			</div>
		</div>
		<div class="OptionsContainer">
			<div class="OptionsContent">
				<div id="readyCheck" class="IngameOptionsContainer" onclick="readyCheck();">
					<div class="IngameOptionsContent">
						準備完成
					</div>
				</div>
				<div id="abc" class="IngameOptionsContainer" onclick="">
					<div id="exitViallge" class="IngameOptionsContent">
						離開村莊
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="IngameOptions" class="Options">
		<div class="OptionsTitleContainer">
			<div id="IngameOptionsTitle" class="OptionsTitleContent">
				遊戲選項 (投票、占卜等等)
			</div>
		</div>
		<div class="OptionsContainer">
			<div class="OptionsContent">
				<div id="item1" class="IngameOptionsContainer" onclick="">
					<div class="IngameOptionsContent">
						Item 1
					</div>
				</div>
				<div id="item2" class="IngameOptionsContainer" onclick="">
					<div id="exitViallge" class="IngameOptionsContent">
						Item2
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

<div class="footer">
</div>

<!--script src="http://code.jquery.com/jquery-1.5.js"></script-->
<script src="md5.js"></script>
<script src="ingame.js"></script>
