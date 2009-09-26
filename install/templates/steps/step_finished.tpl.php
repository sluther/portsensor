<h2>Congratulations!  Setup Complete.</h2>

<form action="index.php" method="POST">
<input type="hidden" name="step" value="{$smarty.const.STEP_FINISHED}">
<input type="hidden" name="form_submit" value="1">

<H3>PortSensor Server is ready for business!</H3>
<b>Login:</b> admin<br>
<b>Password:</b> superuser<br>
<br>

<a href="{devblocks_url}{/devblocks_url}">Log in!</a><br>
<br>

</form>