<h1>Sensor Events</h1>
<b>Server Time:</b> {$now_secs|date_format:"%b %e - %I:%M%p"}<br>
<br>

<h2>{$device->name} :: {$sensor->name}</h2>

{if !empty($plots)}
<table cellpadding="2" cellspacing="0" border="0">
{foreach from=$plots item=plot}
	{if 1==$plot->status}
		{assign var=sensor_style value="status_warning"}
	{elseif 2==$plot->status}
		{assign var=sensor_style value="status_critical"}
	{else}
		{assign var=sensor_style value="status_ok"}
	{/if}

	<tr>
		<td align="right" style="padding-right:10px;" valign="top">
			{if 1==$plot->status}
				<img src="{devblocks_url}c=resource&p=app.core&f=images/led_yellow.png{/devblocks_url}" align="top" border="0">
			{elseif 2==$plot->status}
				<img src="{devblocks_url}c=resource&p=app.core&f=images/led_red.png{/devblocks_url}" align="top" border="0">
			{else}
				<img src="{devblocks_url}c=resource&p=app.core&f=images/led_green.png{/devblocks_url}" align="top" border="0">
			{/if}
		</td>
		<td align="right" style="padding-right:10px;" valign="top">
		{if date('Y-m-d',$now_secs)==date('Y-m-d',$sensor->last_updated)} {* Today *}
			{$plot->log_date|date_format:"%I:%M%p"}
		{else}
			{$plot->log_date|date_format:"%b %e - %I:%M%p"}
		{/if}
		</td>
		<td class="{$sensor_style}">{$plot->metric}</td>
	</tr>
{/foreach}
</table>
{else}
	No events logged.
{/if}

<br>
