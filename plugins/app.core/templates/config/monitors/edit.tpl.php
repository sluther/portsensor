{include file="$path/config/menu.tpl.php"}
<br>

<form action="{devblocks_url}{/devblocks_url}" method="post">
<input type="hidden" name="c" value="config">
<input type="hidden" name="a" value="saveMonitor">
<input type="hidden" name="id" value="{$monitor->id}">
<input type="hidden" name="do_delete" value="0">

<div class="block">
<h2>Modify Monitor</h2>

<b>Monitor Name:</b><br>
<input type="text" name="monitor_name" value="{$monitor->name|escape}" maxlength="64" size="32"><br>
<br>

<b>Monitor ID:</b> (lowercase, no spaces. e.g.: xev.monitor)<br>
<input type="text" name="monitor_guid" value="{$monitor->guid|escape}" maxlength="32" size="32"><br>
<br>

<b>Monitor Password:</b><br>
<input type="text" name="monitor_secret_key" value="{if empty($monitor->id)}{$gen_secret_key}{else}{$monitor->secret_key|escape}{/if}" maxlength="40" size="32"><br>
<br>

<b>M.I.A. After (secs):</b> (0=disabled)<br>
<input type="text" name="monitor_mia" value="{$monitor->mia_secs|escape}" maxlength="5" size="4"><br>
<br>

<button type="submit"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> {$translate->_('common.save_changes')}</button> 
{if !empty($monitor->id)}<button type="button" onclick="if(confirm('Are you sure you want to delete this monitor?')){literal}{{/literal}this.form.do_delete.value='1';this.form.submit();{literal}}{/literal}"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete2.gif{/devblocks_url}" align="top" border="0"> Delete</button>{/if}
<button type="button" onclick="document.location = '{devblocks_url}c=config&a=monitors{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/delete.gif{/devblocks_url}" align="top" border="0"> Cancel</button> 
</div>

</form>
