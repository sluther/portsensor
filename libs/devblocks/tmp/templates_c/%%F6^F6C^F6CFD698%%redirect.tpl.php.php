<?php /* Smarty version 2.6.14, created on 2009-09-26 01:40:51
         compiled from steps/redirect.tpl.php */ ?>
<html>
<head>
	<title>PortSensor - Web Installer</title>
	<link rel="stylesheet" href="install.css" type="text/css">
	<meta http-equiv="refresh" content="1;url=index.php?step=<?php echo $this->_tpl_vars['step']; ?>
">
	
<script language="javascript">

 function onward() <?php echo '{'; ?>

 	setTimeout("window.location.replace('index.php?step=<?php echo $this->_tpl_vars['step']; ?>
')",2);
 <?php echo '}'; ?>


</script>	
</head>

<body onload="onward()">

<H1>Installing PortSensor Server 3.0</H1>
Please wait...

</body>
</html>