<html>
<head>
	<title>PortSensor - Web Installer</title>
	<link rel="stylesheet" href="install.css" type="text/css">
	<meta http-equiv="refresh" content="1;url=index.php?step={$step}">
	
<script language="javascript">

 function onward() {literal}{{/literal}
 	setTimeout("window.location.replace('index.php?step={$step}')",2);
 {literal}}{/literal}

</script>	
</head>

<body onload="onward()">

<H1>Installing PortSensor Server 3.0</H1>
Please wait...

</body>
</html>