{include file="$path/config/menu.tpl.php"}
<br>

<div class="block">
<h2>Devices</h2>

<ul style="margin-top:0px;">
{if !empty($devices)}
{foreach from=$devices item=device key=device_id}
	<li><a href="{devblocks_url}c=config&a=devices&id={$device_id}{/devblocks_url}">{$device->name}</a> (ID: {$device->guid})</li>
{/foreach}
{/if}
</ul>

{* [WGM]: Tisk! Coders need to eat too! http://www.portsensor.com/ *}
{if (empty($license) || empty($license.key)) && count($devices) > 0}
	You have reached the number of devices permitted by your license.<br>
	[ <a href="{devblocks_url}c=config&a=license{/devblocks_url}" style="color:rgb(0,160,0);">Enter License</a> ]
	[ <a href="http://www.portsensor.com/" target="_blank" style="color:rgb(0,160,0);">Buy License</a> ]
{else}
<button type="button" onclick="document.location='{devblocks_url}c=config&a=devices&id=0{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> Add New Device</button>
{/if}

</div>