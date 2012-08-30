	function submitform()
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
				{	//alert("message send");
				}
			}
			xmlhttp.open("POST","imsend.php",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("message="+document.forms["imsend"]["message"].value);
			document.forms["imsend"]["message"].value = "";
			document.getElementById('message').focus();
		}
	}
	
	function requestmesg()
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
			{	if(!(xmlhttp.responseText == null || xmlhttp.responseText == ""))
				{	var jsonreply = JSON.parse(xmlhttp.responseText);
					var i;
					for (i = 0; i < jsonreply.id.length; i++)
					{	document.getElementById("PlayersChatMessagesContent").innerHTML = jsonreply.mesg[i] + "<br>" + document.getElementById("PlayersChatMessagesContent").innerHTML;
					}
					
					document.forms["imsend"]["lastid"].value = jsonreply.id[jsonreply.id.length - 1];
					
				}
				setTimeout(requestmesg, 1000);
			}
		}
		
		xmlhttp.open("GET","imlist.php?lastid="+document.forms["imsend"]["lastid"].value, false);
		xmlhttp.send();
	}

	function myTimer()
	{	var d=new Date();
		var t=d.toLocaleTimeString();
		//document.getElementById("theTimeIs").innerHTML=calcTime(-5);
		document.getElementById("theTimeIs").innerHTML=t;
	}
	
	// function to calculate local time
	// in a different city
	// given the city's UTC offset
	//function calcTime(city, offset) {
	function calcTime(offset) {

		// create Date object for current location
		d = new Date();

		// convert to msec
		// add local time zone offset
		// get UTC time in msec
		utc = d.getTime() + (d.getTimezoneOffset() * 60000);

		// create new Date object for different city
		// using supplied offset
		nd = new Date(utc + (3600000*offset));

		// return time as a string
		//return "The local time in " + city + " is " + nd.toLocaleString();
		return nd.toLocaleString();

	}

	// get Bombay time
	//alert(calcTime('Bombay', '+5.5'));
	
	function enableDiv(theThing)
	{	document.getElementById(theThing).style.display = 'block';
	}
	
	function disableDiv(theThing)
	{	document.getElementById(theThing).style.display = 'none';
	}
	
	
