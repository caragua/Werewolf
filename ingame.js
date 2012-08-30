// For using with free web hosts
// removes the stupid "analizer" or sosmething that they put on the final line that affects the JSON string.
function removeTrash(str)
{	/*var indice = str.indexOf("<!--");
	var newstring = str.substring(0, indice);
	return newstring;*/
	
	return str.substring(0,  str.indexOf("<!--"));
}

// Click to select a div's content (by it's id)
// found here: http://stackoverflow.com/questions/1173194/select-all-div-text-with-single-mouse-click
function selectText(targetto)
{	if (document.selection)
	{	var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(targetto));
		range.select();
	}
	else if (window.getSelection)
	{	var range = document.createRange();
		range.selectNode(document.getElementById(targetto));
		window.getSelection().addRange(range);
	}
}

function setCookie(c_name, value, exdays)
{	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
	document.cookie = c_name + "=" + c_value;
}

function getCookie(c_name)
{	var i, x, y, ARRcookies = document.cookie.split(";");
	for(i = 0; i < ARRcookies.length; i++)
	{	x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g,"");
		if(x == c_name)
		{	return unescape(y);
		}
	}
}

function startUpCheck()
{	var pid = getCookie("wlfid"), pnick = getCookie("wlfnick"), pdate = getCookie("wlfdate");
	if(getCookie("wlfchk") == hex_md5(pid + pnick + pdate))
	{	//alert("帳號資料確認OK");
		if(!(getCookie("wlfroom") == "" || getCookie("wlfroom") == null))
		{	//alert("進入房間");
			//alert(getCookie("wlfavt"));
			playerJoinGame();
			chatRequestWaiting();
			playerRequestWaiting();
			return true;
		}
		else
		{	alert("房間資訊錯誤");
			return false;
		}
	}
	else
	{	alert("帳號確認錯誤，您有登入了嗎?");
		return false;
	}
}

function playerJoinGame()
{	var xmlhttp;
	if (window.XMLHttpRequest)
	{	// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{	// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{	//alert(xmlhttp.responseText);
			//if(removeeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == ""))
			if(xmlhttp.responseText == null || xmlhttp.responseText == "")
			{	alert("加入失敗!");
			}
			else
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
				
					//var jsonreply = JSON.parse(xmlhttp.responseText);
					setCookie("wlfpid", xmlhttp.responseText, 1);
					//alert("加入成功!" + xmlhttp.responseText);
				}
				catch (exc)
				{	// remember to comment this on free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
		}
	}
	
	xmlhttp.open("POST","ingame.playerJoinGame.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("userid=" + getCookie("wlfid") + "&gameid=" + getCookie("wlfroom") + "&usernick=" + getCookie("wlfnick") + "&useravatar=" + getCookie("wlfavt"));
}

function readyCheck()
{	if(true)
	{	var xmlhttp;
		if (window.XMLHttpRequest)
		{	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{	//alert(xmlhttp.responseText);
				/*updatePlayerList();
				if(document.getElementById('readyCheck').value == "準備完成")
				{	document.getElementById('readyCheck').value == "準備中";
				}
				else
				{	document.getElementById('readyCheck').value == "準備完成";
				}*/
				if(xmlhttp.responseText == "OK")
				{	document.getElementById('readyCheck').style.display = 'none';
				}
			}
		}
		xmlhttp.open("POST","ingame.playerGameStatus.php", true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		var checkState = 0;
		/*if(document.getElementById('readyCheck').value == "準備完成")
		{	checkState = 0;
		}
		else
		{	checkState = 1;
		}*/
		//xmlhttp.send("pid="+getCookie("wlfpid")+"&state="+checkState);
		xmlhttp.send("pid="+getCookie("wlfpid")+"&state=1");
	}
	else
	{	alert("請先登入與聊天");
	}
}

function chatRequestWaiting()
{	var xmlhttp;
	if (window.XMLHttpRequest)
	{	// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{	// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{	//if(!(removeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == "")))
			if(!(xmlhttp.responseText == null || xmlhttp.responseText == ""))
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
				
					var jsonreply = JSON.parse(xmlhttp.responseText);
					var i;
					for (i = 0; i < jsonreply.id.length; i++)
					{	// type #1 = System Message, Game announcements or commands
						if(jsonreply.type[i] == 99)
						{	var showMesg = "";
						
							if(jsonreply.mesg[i] == 'initgame')
							{	initGame();
								showMesg = "遊戲開始";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							}
							else if(jsonreply.mesg[i] == 'stopchat')
							{	document.getElementById('ChatContainer').style.height = '95%';
								document.getElementById('ChatSendContainer').style.display = 'none';
							}
							else if(jsonreply.mesg[i] == 'startchat')
							{	document.getElementById('ChatContainer').style.height = '80%';
								document.getElementById('ChatSendContainer').style.display = 'block';
							}
							else if(jsonreply.mesg[i] == 'processing')
							{	showMesg = "系統處理中，請稍待";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer1'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							}
							else if(jsonreply.mesg[i] == 'day')
							{	showMesg = "白天";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							
								chargeTestMessage("請選擇要吊刑的對象");
							}
							else if(jsonreply.mesg[i] == 'dayvote')
							{	showMesg = "請投票";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
								
								chargeTestMessage("請選擇要吊刑的對象");
							}
							else if(jsonreply.mesg[i] == 'night')
							{	showMesg = "夜晚";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							
								chargeTestMessage("請選擇要咬的對象");
							}
							else if(jsonreply.mesg[i] == 'nightvote')
							{	showMesg = "請選擇目標";
								document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer'><div class='PlayerChatBobbleSpacer'></div><div class='SystemChatBobbleContainer'>" + showMesg + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							
								chargeTestMessage("請選擇要咬的對象");
							}
							
							
							
						}
						// type #2 = Player specific message, private messages (like divine result)
						else if(jsonreply.type[i] == 2)
						{
						}
						// type #0 = General chat messages
						else
						{	//document.getElementById("ChatContent").innerHTML = "<div class='PlayersChatMessageBobble'><div class='PlayersChatMessageHeader'>" + jsonreply.nick[i] + ":</div><div class='PlayersChatMessageeContentHolder'><div class='PlayersChatMessageContent'>" + jsonreply.mesg[i] + "</div><div class='footer'></div></div><div class='footer'></div></div><div class='spacer3'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							//document.getElementById("ChatContent").innerHTML = "<div class='PlayerChatBobbleSpacer'></div><div class='PlayerChatBobbleContainer' id='" + jsonreply.id[i] + "' onclick='selectText(" + jsonreply.id[i] + ");'><div class='PlayerChatBobbleNick'>" + jsonreply.nick[i] + ":</div><div class='PlayerChatBobbleSpacer'></div><div class='PlayerChatBobbleMessage0'>" + jsonreply.mesg[i] + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div><div style='float:up; width: 100%;'><hr width='94%'></hr></div>" + document.getElementById("ChatContent").innerHTML;
							//document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer' id='" + jsonreply.id[i] + "' onclick='selectText(" + jsonreply.id[i] + ");'><div class='PlayerChatBobbleNick" + jsonreply.size[i] + "'>" + jsonreply.nick[i] + ":</div><div class='PlayerChatBobbleSpacer'></div><div class='PlayerChatBobbleMessage" + jsonreply.size[i] + "'>" + jsonreply.mesg[i] + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
							document.getElementById("ChatContent").innerHTML = "<hr width='94%'></hr><div class='PlayerChatBobbleContainer' id='" + jsonreply.id[i] + "' onclick='selectText(" + jsonreply.id[i] + ");'><div class='PlayerChatBobbleNick0'>" + jsonreply.nick[i] + ":</div><div class='PlayerChatBobbleSpacer'></div><div class='PlayerChatBobbleMessage" + jsonreply.size[i] + "'>" + jsonreply.mesg[i] + "</div><div class='PlayerChatBobbleSpacer'></div><div class='footer'></div></div>" + document.getElementById("ChatContent").innerHTML;
						}
					
					
					
					}
					
					document.forms["imsend"]["lastid"].value = jsonreply.id[jsonreply.id.length - 1];
					
					//alert(xmlhttp.responseText);
				}
				catch (exc)
				{	// remember to cancel this on free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
			setTimeout(chatRequestWaiting, 1000);
		}
	}
	
	xmlhttp.open("POST","ingame.chatRequestWaiting.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("lastid="+document.forms["imsend"]["lastid"].value + "&gameid=" + getCookie("wlfroom") + "&playerid=" + getCookie("wlfpid"));
}

function playerRequestWaiting()
{	var xmlhttp;
	if (window.XMLHttpRequest)
	{	// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{	// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{	//if(!(removeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == "")))
			if(!(xmlhttp.responseText == null || xmlhttp.responseText == ""))
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
				
					var jsonreply = JSON.parse(xmlhttp.responseText);
					var i;
					document.getElementById("PlayerListContent").innerHTML = "";
					for (i = 0; i < jsonreply.id.length; i++)
					{	//document.getElementById("PlayerListContent").innerHTML = jsonreply.nick[i] + "<br>" + document.getElementById("PlayerListContent").innerHTML;
						var avat = "";
						if(jsonreply.avatar[i] == null || jsonreply.avatar[i] == "null" || jsonreply.avatar[i] == "")
						{	avat = "default.jpg";
						}
						else
						{	avat = jsonreply.avatar[i];
						}
						var stat = "";
						if(jsonreply.status[i] == 0)
						{	stat = "等待中";
						}
						else if(jsonreply.status[i] == 1)
						{	stat = "準備完成";
						}
						else if(jsonreply.status[i] == 2)
						{	stat = "生存中";
						}
						else if(jsonreply.status[i] == 3)
						{	stat = "死亡";
						}
						if(jsonreply.accessTime[i] > 2)
						{	stat = stat + " (離線)";
						}
						//document.getElementById("PlayerListContent").innerHTML = "<div class='WaitingListBobbleSpacer'></div><div class='WaitingListBobbleContainer'><img class='WaitingListBobbleAvatar' src='images/" + avat + "'></img><div class='WaitingListBobbleSpacerVert'></div><div class='WaitingListBobbleTextContainer'><div class='WaitingListBobbleNick'>" + jsonreply.nick[i] + "</div><div class='WaitingListBobbleStatus'>" + stat + "</div><div class='footer'></div></div><div class='footer'></div></div><div class='footer'></div>" + document.getElementById("PlayerListContent").innerHTML;
						document.getElementById("PlayerListContent").innerHTML = "<hr width='90%'></hr><div class='WaitingListBobbleContainer'><div class='WaitingListBobbleSpacerVert1'></div><img class='WaitingListBobbleAvatar' src='images/" + avat + "'></img><div class='WaitingListBobbleSpacerVert'></div><div class='WaitingListBobbleTextContainer'><div class='WaitingListBobbleTest'>"  + " #" + jsonreply.id[i] + "<br>" + jsonreply.nick[i] + "</div>" + stat + "<div class='footer'></div></div><div class='footer'></div></div><div class='footer'></div>" + document.getElementById("PlayerListContent").innerHTML;
						//document.getElementById("PlayerListContent").innerHTML = "<hr width='90%'></hr><div class='WaitingListBobbleContainer'><img class='WaitingListBobbleAvatar' src='images/" + avat + "'></img><div class='WaitingListBobbleSpacerVert'></div><div class='WaitingListBobbleTextContainer'><div class='WaitingListBobbleNick'>"  + " (#" + jsonreply.id[i] + ")<br>" + jsonreply.nick[i] + "</div><div class='WaitingListBobbleStatus'>" + stat + "</div><div class='footer'></div></div><div class='footer'></div></div><div class='footer'></div>" + document.getElementById("PlayerListContent").innerHTML;
					}
					//alert(xmlhttp.responseText);}
				}
				catch (exc)
				{	// remember to comment this for free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
			else
			{	document.getElementById("PlayerListContent").innerHTML = "";
			}
			setTimeout(playerRequestWaiting, 1000);
		}
	}
	
	xmlhttp.open("POST","ingame.playerRequestWaiting.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("&gameid=" + getCookie("wlfroom"));
}

function chatSendWaiting($fontsize)
{	if(!(document.forms["imsend"]["message"].value == "" || document.forms["imsend"]["message"].value == ""))
	{	var xmlhttp;
		if (window.XMLHttpRequest)
		{	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{	//alert(xmlhttp.responseText);
			}
		}
		
		var txt = document.forms["imsend"]["message"].value;
		var txt1 = txt;
		var chek = true;
		
		do
		{	txt = txt1.replace("&", "AAAA-NNNN-DDDD");
			if(txt == txt1)
			{	chek = false;
			}
			else
			{	chek = true;
			}
			txt1 = txt;
		}
		while(chek);
		
		do
		{	txt = txt1.replace("+", "PPPP-LLLL-UUUU-SSSS");
			if(txt == txt1)
			{	chek = false;
			}
			else
			{	chek = true;
			}
			txt1 = txt;
		}
		while(chek);
		
		xmlhttp.open("POST","ingame.chatSendWaiting.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("playerid=" + getCookie("wlfpid") + "&playernick=" + getCookie("wlfnick") + "&message=" + txt + "&gameid=" + getCookie("wlfroom") + "&size=" + $fontsize);
		document.forms["imsend"]["message"].value = "";
		document.getElementById('message').focus();
	}
}


function timeCounterRequest()
{	var xmlhttp;
	if (window.XMLHttpRequest)
	{	// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{	// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{	//if(!(removeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == "")))
			if(!(xmlhttp.responseText == null || xmlhttp.responseText == ""))
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
				
					var jsonreply = JSON.parse(xmlhttp.responseText);
					var $stage = "";
					
					if(jsonreply.stage == 0)
					{	$stage = "白天";
					}
					if(jsonreply.stage == 1)
					{	$stage = "投票處刑";
					}
					if(jsonreply.stage == 2)
					{	$stage = "晚上";
					}
					if(jsonreply.stage == 3)
					{	$stage = "選擇行動";
					}
					
					document.getElementById("ChatTitleContent").innerHTML = jsonreply.name + " [" + $stage + "] [剩餘: " + jsonreply.remaining + " 秒]";
					//for (i = 0; i < jsonreply.id.length; i++)
					//{	document.getElementById("ChatTitleContent").innerHTML = "<div class='WaitingListBobbleContainer'><img class='WaitingListBobbleAvatar' src='images/" + avat + "'></img><div class='WaitingListBobbleSpacerVert'></div><div class='WaitingListBobbleTextContainer'><div class='WaitingListBobbleNick'>" + jsonreply.nick[i] + "</div><div class='WaitingListBobbleStatus'>" + stat + "</div><div class='footer'></div></div><div class='footer'></div></div><div class='footer'></div>" + document.getElementById("PlayerListContent").innerHTML;
					//}
				}
				catch (exc)
				{	// remember to comment this for free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
			else
			{	document.getElementById("ChatTitleContent").innerHTML = "村莊名稱";
			}
			setTimeout(timeCounterRequest, 1000);
		}
	}
	
	xmlhttp.open("POST","ingame.timeCounter.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("&gameid=" + getCookie("wlfroom"));
}

function initGame()
{	timeCounterRequest();
	document.getElementById('WaitOptions').style.display = 'none';
	document.getElementById('waitingChat').style.display = 'none';
	document.getElementById('IngameOptions').style.display = 'block';
	document.getElementById('ingameChat').style.display = 'block';
	
}

//for testing use only
function chargeTestMessage($value)
{	document.getElementById("IngameOptionsTitle").innerHTML = $value;
}

function chargeVoteOptions()
{
}

function chargeBiteOptions()
{
}

function chargeDivineOptions()
{
}

function clearOptions()
{
}

function sendDeathMessage()
{
}







function aboutMe()
{	alert("201208261401");
}

setTimeout(startUpCheck, 1000);
//setTimeout(playerWaitChatRequest, 2000);
//setTimeout(playerWaitList, 2000);