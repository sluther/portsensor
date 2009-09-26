<div class="block">
<h1>Login</h1>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="login">
<input type="hidden" name="a" value="doLogin">

<b>Login/Feed:</b><br>
<input type="text" name="login" value="" size="32"><br>
<br>

<b>Password:</b><br>
<input type="password" name="password" value="" size="16"><br>
<br>

<button type="submit">{$translate->_('common.signon')|capitalize}</button>
<br>

</form>

</div>