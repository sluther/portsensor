{include file="$path/config/menu.tpl.php"}
<br>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="saveFeed">
<input type="hidden" name="id" value="{$feed->id}">
<input type="hidden" name="do_delete" value="0">

<div class="block">
<h2>Modify Feed</h2>

<b>Feed Name:</b><br>
<input type="text" name="feed_name" value="{$feed->name|escape}" maxlength="64" size="32"><br>
<br>

<b>Feed ID:</b> (lowercase, no spaces. e.g.: west_coast_dc)<br>
<input type="text" name="feed_guid" value="{$feed->guid|escape}" maxlength="32" size="32"><br>
<br>

<b>Feed Password:</b> (optional)<br>
<input type="text" name="feed_secret_key" value="{$feed->secret_key|escape}" maxlength="40" size="32"><br>
<br>

<b>Show Devices:</b> (<a href="javascript:;" onclick="checkAll('cfgFeedDevices');">all</a>)<br>
<div class="subtle2" id="cfgFeedDevices">
	{foreach from=$devices item=device key=device_id}
		<label>
		<input type="checkbox" name="devices[]" value="{$device->id}" {if isset($feed_devices.$device_id)}checked{/if}> 
		<img src="{devblocks_url}c=resource&p=app.core&f=images/server_network.png{/devblocks_url}" align="top" border="0"> 
		{$device->name}
		</label>
		<br>
	{/foreach}
</div>
<br>

<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> {$translate->_('common.save_changes')}</button> 
{if !empty($feed->id)}<button type="button" onclick="if(confirm('Are you sure you want to delete this feed?')){literal}{{/literal}this.form.do_delete.value='1';this.form.submit();{literal}}{/literal}"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete2.gif{/devblocks_url}" align="top" border="0"> Delete</button>{/if}
<button type="button" onclick="document.location = '{devblocks_url}c=config&a=feeds{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete.gif{/devblocks_url}" align="top" border="0"> Cancel</button> 
</div>

</form>
