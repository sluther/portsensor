{include file="$path/config/menu.tpl.php"}
<br>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="saveGeneral">

<div class="block">
<h2>General Settings</h2>

<b>Org. Name:</b> (e.g., WebGroup Media LLC.)<br>
<input type="text" name="company_name" value="{$settings->get('company_name','')}" maxlength="64" size="64"><br>
<br>

<b>Logo URL:</b> (leave blank for default)<br>
<input type="text" name="logo_url" value="{$settings->get('logo_url','')}" maxlength="255" size="64"><br>
<br>

<a href="javascript:;" onclick="toggleDiv('cfgGenAdminPw');">Change Admin Password</a><br>

<blockquote style="margin:5px;margin-left:20px;display:none;" id="cfgGenAdminPw">
	<b>New Admin Password:</b> (leave blank for unchanged)<br>
	<input type="password" name="password" value="" size="24"><br>
	<br>
	
	<b>Confirm New Password:</b> <br>
	<input type="password" name="password2" value="" size="24"><br>
</blockquote>
<br>

<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> {$translate->_('common.save_changes')}</button>
</div>
</form>

<br>

{if $event_count}
<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="clearEvents">

<div class="block">
<h2>Events</h2>
<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete.gif{/devblocks_url}" align="top" border="0"> Clear Event Log</button>
</div>
</form>
{/if}