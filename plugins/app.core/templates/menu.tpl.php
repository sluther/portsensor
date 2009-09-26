{if !empty($visit)}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="headerMenu">
	<tr>
		{assign var=rows value=0}
			{foreach from=$pages item=m}
				{if !empty($m->manifest->params.menutitle)}
					{math assign=rows equation="x+1" x=$rows}
					<td width="1%" nowrap="nowrap" style="padding-left:10px;padding-right:10px;border-right:1px solid rgb(102, 102, 255);" {if $page->id==$m->id}id="headerMenuSelected"{/if}><a href="{devblocks_url}c={$m->manifest->params.uri}{/devblocks_url}">{$m->manifest->params.menutitle|lower}</a></td>
				{/if}
			{/foreach}
		<td width="{math equation="100-x" x=$rows}%"><img src="{devblocks_url}c=resource&p=app.core&f=images/spacer.gif{/devblocks_url}" height="22" width="1"></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr><td class="headerUnderline"><img src="{devblocks_url}c=resource&p=app.core&f=images/spacer.gif{/devblocks_url}" height="5" width="1"></td></tr>
</table>
<img src="{devblocks_url}c=resource&p=app.core&f=images/spacer.gif{/devblocks_url}" height="5" width="1"><br>
{/if}
