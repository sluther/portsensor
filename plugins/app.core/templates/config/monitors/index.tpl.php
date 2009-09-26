{include file="$path/config/menu.tpl.php"}
<br>

<div class="block">
<h2>Monitors</h2>

<ul style="margin-top:0px;">
{if !empty($monitors)}
{foreach from=$monitors item=monitor key=monitor_id}
	<li><a href="{devblocks_url}c=config&a=monitors&id={$monitor_id}{/devblocks_url}">{$monitor->name}</a> (ID: {$monitor->guid})</li>
{/foreach}
{/if}
</ul>

<button type="button" onclick="document.location='{devblocks_url}c=config&a=monitors&id=0{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> Add New Monitor</button>

</div>