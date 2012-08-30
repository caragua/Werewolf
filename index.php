<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

<LINK href="index.css" rel="stylesheet" type="text/css" media="screen">

<div class="spacer1">
</div>

<div id="left">
</div>

<div class="spacer1">
</div>

<div id="center">

	?

</div>

<div class="spacer1">
</div>

<div id="right">

	<div id="PlayersFree">
		<div id="PlayersFreeTitle">
			<div id="PlayersFreeTitleContent">
				等待中的玩家
			</div>
			<div class="footer"></div>
		</div>
		<div id="PlayersFreeListing">
			<div id="PlayersFreeListingContent">
			</div>
			<div class="footer"></div>
		</div>
	</div>
	
	<div class="spacer2"></div>
	
	<div id="PlayersChat">
		<div id="PlayersChatTitle">
			<div id="PlayersChatTitleContent">
				聊天室 (<a href="#" onclick="showLogin(); return false;">登入</a> 或 <a href="#" onclick="showRegister(); return false;">註冊</a>)
			</div>
			<div class="footer"></div>
		</div>
		<div id="PlayersChatMessagesSend">
			<div id="PlayersChatMessagesSendContent">
				<form id='imsend' action='#' onsubmit='sendChatWaiting(); return false;'>
					<input type='text' name='message' id='message' style='width: 100%; height: 100%'/>
					<input type='hidden' name='lastid' value=0 />
				</form>
			</div>
			<div class="footer"></div>
		</div>
		<div id="PlayersChatMessages">
			<div id="PlayersChatMessagesContent">
				<!--div class="footer"></div-->
			</div>
			<div class="footer"></div>
		</div>
	</div>
	
	<div id="PlayersChatLoginContainer">
		<div id="PlayersChatLoginTitle">
			<div id="PlayersChatLoginTitleContent">
				登入帳號 (<a href="#" onclick="cancelLogin(); return false;">取消</a>)
			</div>
			<div class="footer"></div>
		</div>
		<form id='playerLogin' action='#' onsubmit='if(document.forms["playerLogin"]["playerEmail"].value.length > 0 && document.forms["playerLogin"]["playerEmail"].value != "輸入信箱" && document.forms["playerLogin"]["playerPass"].value.length > 0 && document.forms["playerLogin"]["playerPass"].value != "輸入密碼"){playerLogin();} return false;'>
			<input type='text' name='playerEmail' id='playerEmail' style='width: 100%; height: 10.53%;' value="輸入信箱" onfocus="this.value='';"/>
			<input type='text' name='playerPass' id='PlayerPass' style='width: 100%; height: 10.53%;' value="輸入密碼" onfocus="this.value=''; this.type='password';"/>
			<input type='submit' value='登入' style='width:100%; height: 10.53%;'/>
		</form>
		<div class="footer"></div>
	</div>
	
	
	
	<div id="PlayerRegisterContainer">
		<div id="PlayerRegisterTitle">
			<div id="PlayerRegisterTitleContent">
				註冊帳號 (<a href="#" onclick="cancelRegister(); return false;">取消</a>)
			</div>
			<div class="footer"></div>
		</div>
		<form id='playerRegister' action='#' onsubmit='if(document.forms["playerRegister"]["playerEmail"].value.length > 0 && document.forms["playerRegister"]["playerEmail"].value != "輸入信箱" && document.forms["playerRegister"]["playerPass"].value.length > 0 && document.forms["playerRegister"]["playerPass"].value != "輸入密碼" && document.forms["playerRegister"]["playerNick"].value.length > 0 && document.forms["playerRegister"]["playerNick"].value != "輸入暱稱"){playerRegister();} return false;'>
			<input type='text' name='playerEmail' id='playerEmail' style='width: 100%; height: 10.53%;' value="輸入信箱" onfocus="this.value='';"/>
			<input type='text' name='playerPass' id='playerPass' style='width: 100%; height: 10.53%;' value="輸入密碼" onfocus="this.value=''; this.type='password';"/>
			<input type='text' name='playerNick' id='playerNick' style='width: 100%; height: 10.53%;' value="輸入暱稱" onfocus="this.value='';"/>
			<input type='submit' value='註冊' style='width:100%; height: 10.53%;'/>
		</form>
		<div class="footer"></div>
	</div>
	
</div>

<div class="footer">
</div>

<!--script src="http://code.jquery.com/jquery-1.5.js"></script-->
<script src="md5.js"></script>
<script src="index.js"></script>
