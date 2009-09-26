{include file="$tpl_path/header.tpl.php"}

<table cellspacing="0" cellpadding="2" border="0" width="100%">
	<tr>
		<td align="left" valign="bottom">
			<img src="{devblocks_url}c=resource&p=app.core&f=images/logo.jpg{/devblocks_url}">
		</td>
		<td align="right" valign="bottom" style="line-height:150%;">
		{if empty($visit)}
			[<a href="{devblocks_url}c=login{/devblocks_url}">{$translate->_('common.signon')|lower}</a>]
		{else}
			Logged in as {if $visit->is_admin}<b>admin</b>{elseif $visit->is_feed}<b>{$visit->is_feed->guid}</b>{/if} 
			{if $visit->is_admin}
			[ <a href="{devblocks_url}c=config{/devblocks_url}">{$translate->_('common.setup')|lower}</a> ]
			{/if}
			[ <a href="{devblocks_url}c=login&a=signout{/devblocks_url}">{$translate->_('common.signoff')|lower}</a> ]
			<br>
		{/if} 
		</td>
	</tr>
</table>

{include file="$tpl_path/menu.tpl.php"}

{if !empty($page) && $page->isVisible()}
	{$page->render()}
{else}
	<h1>404</h1>
	{$translate->_('common.404.message')}
	<br>
{/if}

{include file="$tpl_path/footer.tpl.php"}
