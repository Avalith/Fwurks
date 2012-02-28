<div id='breadcrumbs'>
	{foreach $breadcrumbs as $b}
		{if !$b@last}{if $b.type != 'link'}<a href="{url for=$b.link}">{/if}{$b.title}{if $b.type != 'link'}</a>{/if} &gt; {else}{$b.title}{/if} 
	{/foreach}
</div>