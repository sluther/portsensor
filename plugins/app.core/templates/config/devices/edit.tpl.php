{include file="$path/config/menu.tpl.php"}
<br>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="saveDevice">
<input type="hidden" name="id" value="{$device->id}">
<input type="hidden" name="do_delete" value="0">

<div class="block">
<h2>Modify Device</h2>

<b>Device Name:</b><br>
<input type="text" name="device_name" value="{$device->name|escape}" maxlength="64" size="32"><br>
<br>

<b>Device ID:</b> (lowercase, no spaces. e.g.: xev.wgm)<br>
<input type="text" name="device_guid" value="{$device->guid|escape}" maxlength="32" size="32"><br>
<br>

{if (empty($license) || empty($license.key)) && !$device->id && count($devices)>0}{* Don't make us flip burgers! *}{else}<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> {$translate->_('common.save_changes')}</button>{/if} 
{if !empty($device->id)}<button type="button" onclick="if(confirm('Are you sure you want to delete this device?')){literal}{{/literal}this.form.do_delete.value='1';this.form.submit();{literal}}{/literal}"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete2.gif{/devblocks_url}" align="top" border="0"> Delete</button>{/if} 
<button type="button" onclick="document.location = '{devblocks_url}c=config&a=devices{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete.gif{/devblocks_url}" align="top" border="0"> Cancel</button> 
</div>

</form>

