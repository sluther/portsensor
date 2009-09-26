{include file="$path/config/menu.tpl.php"}
<br>

<div class="block">
<h2>License Info</h2>
{if empty($license) || empty($license.key)}
	<span style="color:rgb(200,0,0);">No License (Free Mode)</span><br>
	<ul style="margin-top:0px;">
		<li>Limited to 1 Device</li>
	</ul> 
{else}
	<b>Licensed to:</b><br>
	{$license.name}<br>
	<br>
{/if}
</div>
<br>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="saveLicense">

<div class="block">
<h2>Add License</h2>

<b>Paste the product key you received with your order:</b><br>
<textarea rows="5" cols="80" name="key"></textarea><br>
<br>

<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> {$translate->_('common.save_changes')}</button>
</div>
</form>
<br>
