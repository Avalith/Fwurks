{extend file='layout.tpl'}

{block name='actions'}
	{scaffolding_listing params=$_listing class='list'}
	
	{if $_listing.filters}
		<div class="listing_filter">
			{foreach $_listing.filters as $f}
				{html_field type=$f.type name=$f@key data=$f.data value=$f.value class=$f.class 
					show_empty_option=$f.show_empty_option|default:1 empty_option_title=$f.empty_option_title 
					format=$f.format picker=$f.picker 
					multiple=$f.multiple size=$f.size|default:1
					value_field=$f.value_field|default:'id' title_field=$f.title_field|default:'title'}
			{/foreach}
			<a class="button apply" title="{$__globals.ACTIONS.apply_filter}" href=""><span>{$__globals.ACTIONS.apply_filter}</span></a>
			<div class="cleaner">&nbsp;</div>
		</div>
	{/if}
	{if $listing_add || $_listing.actions}
		<div class="menu">
			{$listing_add}
			{foreach $_listing.actions as $a}
				{if $_listing.user_can[$a.permission]}
					<a {if $a.id} id="action-button-{$a.id}"{/if} class="button {$a.class}" title="{$__globals.ACTIONS[$a.title]|default:$a.title}" href="{$a.href|default:$a.title}">
						<span>{$__globals.ACTIONS[$a.title]|default:$a.title}</span>
					</a>
				{/if}
			{/foreach}
			<div class="cleaner">&nbsp;</div>
		</div>
	{/if}
{/block}

{block name='content'}
	{if $_listing.data}<p class="results">{$__globals.FOUND_ROWS} {$_listing.data->total} results</p>{/if}
	{$listing}
{/block}