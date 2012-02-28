<div id='breadcrumbs'>
	{foreach $breadcrumbs as $b}
		{if !$b@last}{if $b.type != 'link'}<p><a href="{url for=$b.link}">{/if}{$b.title}{if $b.type != 'link'}</a></p>{/if} &gt; {else}{$b.title}{/if} 
	{/foreach}
</div>