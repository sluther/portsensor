{include file="$path/config/menu.tpl.php"}
<br>

<div class="block">
<h2>Feeds</h2>

<ul style="margin-top:0px;">
{if !empty($feeds)}
{foreach from=$feeds item=feed key=feed_id}
	<li>
		<a href="{devblocks_url}c=config&a=feeds&id={$feed_id}{/devblocks_url}">{$feed->name}</a><br>
		<blockquote style="margin:2px;margin-left:10px;">{devblocks_url full=true}c=feed&id={$feed->guid}{/devblocks_url}</blockquote>
	</li>
{/foreach}
{/if}
</ul>

<button type="button" onclick="document.location='{devblocks_url}c=config&a=feeds&id=0{/devblocks_url}';"><img src="{devblocks_url}c=resource&p=app.core&f=images/check.gif{/devblocks_url}" align="top" border="0"> Add New Feed</button>
 
</div>
 