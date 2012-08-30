// For using with free web hosts
// removes the stupid "analizer" or sosmething that they put on the final line that affects the JSON string.
function removeTrash(str)
{	/*var indice = str.indexOf("<!--");
	var newstring = str.substring(0, indice);
	return newstring;*/
	
	return str.substring(0,  str.indexOf("<!--"));
}

function showLogin()
{	document.getElementById('PlayersChat').style.display = 'none';
	document.getElementById('PlayersChatLoginContainer').style.display = 'block';
	document.getElementById('PlayerRegisterContainer').style.display = 'none';
}

function cancelLogin()
{	document.getElementById('PlayersChat').style.display = 'block';
	document.getElementById('PlayersChatLoginContainer').style.display = 'none';
	document.getElementById('PlayerRegisterContainer').style.display = 'none';
}

function finishLogin()
{	var pid = getCookie("wlfid"), pnick = getCookie("wlfnick"), pdate = getCookie("wlfdate");
	if(getCookie("wlfchk") == hex_md5(pid + pnick + pdate))
	{	document.getElementById('PlayersChat').style.display = 'block';
		document.getElementById('PlayersChatLoginContainer').style.display = 'none';
		document.getElementById('PlayerRegisterContainer').style.display = 'none';
		document.getElementById('PlayersChatTitleContent').innerHTML = getCookie("wlfnick") + " (<a href='#' onclick='playerLogout(); return false;'>登出</a>)";
	}
}

function showRegister()
{	document.getElementById('PlayersChat').style.display = 'none';
	document.getElementById('PlayersChatLoginContainer').style.display = 'none';
	document.getElementById('PlayerRegisterContainer').style.display = 'block';
}

function cancelRegister()
{	document.getElementById('PlayersChat').style.display = 'block';
	document.getElementById('PlayersChatLoginContainer').style.display = 'none';
	document.getElementById('PlayerRegisterContainer').style.display = 'none';
}

// not in use
function finishRegister()
{	document.getElementById('PlayersChat').style.display = 'block';
	document.getElementById('PlayersChatLoginContainer').style.display = 'none';
	document.getElementById('PlayerRegisterContainer').style.display = 'none';
}

function playerLogin()
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
		{	//if((removeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == ""))
			if((xmlhttp.responseText == null || xmlhttp.responseText == ""))
			{	alert("登入失敗!");
			}
			else
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
								
					var jsonreply = JSON.parse(xmlhttp.responseText);
					
					var logindate = new Date();
					var md5string = jsonreply.id + jsonreply.nickname + logindate;
					// Obtained from here
					//http://anishrana.blogspot.com/2011/09/javascript-md5-encryption.html
					md5string = hex_md5(md5string);
					
					setCookie("wlfid", jsonreply.id, 7);
					setCookie("wlfnick", jsonreply.nickname, 7);
					setCookie("wlfavt", jsonreply.avatar, 7);
					setCookie("wlfdate", logindate, 7);
					setCookie("wlfchk", md5string, 7);
					
					finishLogin();
					
					alert("登入成功!");
				}
				catch (exc)
				{	// remember to comment this when on a free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
		}
	}
	
	xmlhttp.open("POST","playerLogin.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("email="+document.forms["playerLogin"]["playerEmail"].value+"&pass="+hex_md5(document.forms["playerLogin"]["playerPass"].value));
}

function playerLogout()
{	setCookie("wlfid", "", -1);
	setCookie("wlfnick", "", -1);
	setCookie("wlfdate", "", -1);
	setCookie("wlfchk", "", -1);
	setCookie("wlfroom", "", -1);
	setCookie("wlfavt", "", -1);
	document.getElementById('PlayersChatTitleContent').innerHTML = "聊天室 (<a href='#' onclick='showLogin(); return false;'>登入</a> 或 <a href='#' onclick='showRegister(); return false;'>註冊</a>)";
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

function sendChatWaiting()
{	if(!(document.forms["imsend"]["message"].value == "" || document.forms["imsend"]["message"].value == ""))
	{	var pid = getCookie("wlfid"), pnick = getCookie("wlfnick"), pdate = getCookie("wlfdate");
		if(getCookie("wlfchk") == hex_md5(pid + pnick + pdate))
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
			xmlhttp.open("POST","playerWaitChatSend.php",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("pid="+getCookie("wlfid")+"&pnick="+getCookie("wlfnick")+"&message="+document.forms["imsend"]["message"].value);
			document.forms["imsend"]["message"].value = "";
			document.getElementById('message').focus();
		}
		else
		{	alert("請登入以參與聊天");
		}
	}
}

function playerWaitChatRequest()
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
					{	document.getElementById("PlayersChatMessagesContent").innerHTML = "<div class='PlayersChatMessageBobble'><div class='PlayersChatMessageHeader'>" + jsonreply.nick[i] + ":</div><div class='PlayersChatMessageeContentHolder'><div class='PlayersChatMessageContent'>" + jsonreply.mesg[i] + "</div><div class='footer'></div></div><div class='footer'></div></div><div class='spacer3'></div><div class='footer'></div></div>" + document.getElementById("PlayersChatMessagesContent").innerHTML;
					}
					
					document.forms["imsend"]["lastid"].value = jsonreply.id[jsonreply.id.length - 1];
					
					//alert(xmlhttp.responseText);
				}
				catch (exc)
				{	// remember to cancel this on free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
			setTimeout(playerWaitChatRequest, 1000);
		}
	}
	var pid = getCookie("wlfid"), pnick = getCookie("wlfnick"), pdate = getCookie("wlfdate");
	var plrid = "";
	if(getCookie("wlfchk") == hex_md5(pid + pnick + pdate))
	{	plrid = pid;
	}
	else
	{	plrid = "";
	}
	xmlhttp.open("POST","playerWaitChatRequest.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("lastid="+document.forms["imsend"]["lastid"].value + "&playerid=" + plrid);
}

function playerWaitList()
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
					document.getElementById("PlayersFreeListingContent").innerHTML = "";
					for (i = 0; i < jsonreply.id.length; i++)
					{	var avat = "";
						if(jsonreply.avatar[i] == null || jsonreply.avatar[i] == "null" || jsonreply.avatar[i] == "")
						{	avat = "default.jpg";
						}
						else
						{	avat = jsonreply.avatar[i];
						}
						//alert("OK");
						//document.getElementById("PlayersFreeListingContent").innerHTML = jsonreply.nick[i] + "<br>" + document.getElementById("PlayersFreeListingContent").innerHTML;
						document.getElementById("PlayersFreeListingContent").innerHTML = "<div class='WaitingListBobbleContainer'><img class='WaitingListBobbleAvatar' src='images/" + avat + "'></img><div class='WaitingListBobbleSpacerVert'></div><div class='WaitingListBobbleTextContainer'><div class='WaitingListBobbleNick'>" + jsonreply.nick[i] + "</div></div><div class='footer'></div></div><div class='footer'></div>" + document.getElementById("PlayersFreeListingContent").innerHTML;
					
					}
					//alert(xmlhttp.responseText);}
				}
				catch (exc)
				{	// remember to comment this for free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
			else
			{	document.getElementById("PlayersFreeListingContent").innerHTML = "";
			}
			setTimeout(playerWaitList, 5000);
		}
	}
	
	xmlhttp.open("POST","playerWaitList.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send();
}

function playerRegister()
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
		{	//if(removeeTrash(xmlhttp.responseText) == null || removeTrash(xmlhttp.responseText == ""))
			if(xmlhttp.responseText == null || xmlhttp.responseText == "")
			{	alert("註冊失敗!");
			}
			else
			{	try
				{	//var jsonreply = JSON.parse(removeTrash(xmlhttp.responseText));
				
					var jsonreply = JSON.parse(xmlhttp.responseText);
					
					var logindate = new Date();
					var md5string = jsonreply.id + jsonreply.nickname + logindate;
					// Obtained from here
					//http://anishrana.blogspot.com/2011/09/javascript-md5-encryption.html
					md5string = hex_md5(md5string);
					
					setCookie("wlfid", jsonreply.id, 7);
					setCookie("wlfnick", jsonreply.nickname, 7);
					setCookie("wlfdate", logindate, 7);
					setCookie("wlfchk", md5string, 7);
					
					finishLogin();
					
					alert("註冊成功!");
				}
				catch (exc)
				{	// remember to comment this on free host
					alert("喔喔，伺服器出包囉 owo // " + exc + xmlhttp.responseText);
				}
			}
		}
	}
	
	xmlhttp.open("POST","playerRegister.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("email="+document.forms["playerRegister"]["playerEmail"].value+"&pass="+hex_md5(document.forms["playerRegister"]["playerPass"].value) + "&nick=" + document.forms["playerRegister"]["playerNick"].value);
}

function aboutMe()
{	alert("201208191311");
}



setTimeout(finishLogin, 1000);
setTimeout(playerWaitChatRequest, 2000);
setTimeout(playerWaitList, 2000);