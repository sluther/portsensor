<h1>Overview</h1>

<div class="subtle2">
<span class="status_ok"><img src="{devblocks_url}c=resource&p=app.core&f=images/led_green.png{/devblocks_url}" align="top" border="0"> <b>{$total_ok}</b> sensors OK</span><br>
{if $total_warning}<span class="status_warning"><img src="{devblocks_url}c=resource&p=app.core&f=images/led_yellow.png{/devblocks_url}" align="top" border="0"> <b>{$total_warning}</b> sensors WARNING</span><br>{/if}
{if $total_critical}<span class="status_critical"><img src="{devblocks_url}c=resource&p=app.core&f=images/led_red.png{/devblocks_url}" align="top" border="0"> <b>{$total_critical}</b> sensors CRITICAL</span><br>{/if}
</div>
<br>

{if $visit->is_feed}
	<h2>{$visit->is_feed->name}</h2>
{else}
	<h2>All Sensors</h2>
{/if}

{if !empty($devices)}
<table cellpadding="2" cellspacing="0" border="0">
{foreach from=$devices item=device key=device_id}
	<tr>
		<td colspan="5" valign="top">
			<img src="{devblocks_url}c=resource&p=app.core&f=images/server_network.png{/devblocks_url}" align="top" border="0"> 
			<b>{$device->name}</b>
		</td>
	</tr>
	{foreach from=$sensors_by_device.$device_id item=sensor key=sensor_id}
		{if 1==$sensor->status}
			{assign var=sensor_style value="status_warning"}
		{elseif 2==$sensor->status}
			{assign var=sensor_style value="status_critical"}
		{else}
			{assign var=sensor_style value="status_ok"}
		{/if}
		
		<tr>
			<td style="padding-left:25px;" valign="top"></td>
			<td style="padding-left:10px;" valign="top">
				{if 1==$sensor->status}
					<img src="{devblocks_url}c=resource&p=app.core&f=images/led_yellow.png{/devblocks_url}" align="top" border="0">
				{elseif 2==$sensor->status}
					<img src="{devblocks_url}c=resource&p=app.core&f=images/led_red.png{/devblocks_url}" align="top" border="0">
				{else}
					<img src="{devblocks_url}c=resource&p=app.core&f=images/led_green.png{/devblocks_url}" align="top" border="0">
				{/if}
				<a href="{devblocks_url}c=events&id={$sensor_id}{/devblocks_url}">{$sensor->name}</a>
			</td>
			<td style="padding-left:10px;" class="{$sensor_style}" valign="top">
				{if 1==$sensor->status}
					WARNING
				{elseif 2==$sensor->status}
					CRITICAL
				{else}
					OK
				{/if}
			</td>
			<!-- <td style="padding-left:5px;">{$sensor->metric}</td> -->
			<td style="padding-left:10px;" valign="top">
				{if date('Y-m-d',$now_secs)==date('Y-m-d',$sensor->last_updated)} {* Today *}
					{$sensor->last_updated|date_format:'%I:%M%p'}
				{else}
					{$sensor->last_updated|date_format:'%b %e %I:%M%p'}
				{/if}
			</td>
			<td style="padding-left:10px;" class="{$sensor_style}" valign="top">
				{$sensor->output|escape|nl2br}
			</td>
		</tr>
	{/foreach}
{/foreach}
</table>
{else}
	No sensor information has been collected.
{/if}

<br>

