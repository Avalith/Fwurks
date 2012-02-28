{extends file='layout.tpl'}

{block name='actions'}
	{scaffolding_tree params=$_tree}
	{scaffolding_listing params=$_listing class='list'}
	
	<div class="col w2" style="min-width: 500px; margin-bottom: 20px;">
		<div class="inner">
			<h1>Navigation</h1>
			<div class="menu">
				{$tree_add}
				{foreach $_tree.actions as $a}
					{if $_tree.user_can[$a.permission]}
						<a {if $a.id} id="action-button-{$a.id}"{/if} class="button {$a.class}" title="{$__globals->ACTIONS[$a.title]|default:$a.title}" href="{url for=$a.href|default:$a.title}">{$__globals->ACTIONS[$a.title]|default:$a.title}</a>
					{/if}
				{/foreach}
				<div class="cleaner">&nbsp;</div>
			</div>
			{*if $_listing.filters}
				<div class="listing_filter">
					{foreach $_listing.filters as $f}
						{html_field type=$f.type name=$f@key data=$f.data value=$f.value class=$f.class show_empty_option=$f.show_empty_option|default:1 empty_option_title=$f.empty_option_title format=$f.format picker=$f.picker}
					{/foreach}
					<a class="button apply" title="{$__globals->ACTIONS.apply_filter}" href=""><span>{$__globals->ACTIONS.apply_filter}</span></a>
					<div class="cleaner">&nbsp;</div>
				</div>
			{/if*}
			{$tree}
		</div>
	</div>
{/block}

{block name='content'}
	
	
	<div class="col w2" style="min-width: 500px;">
		<h1>Pages</h1>
		<div class="menu actions">
			{$listing_add}
			{if $_listing.actions}
				{foreach $_listing.actions as $a}
					{if $_listing.user_can[$a.permission]}
						<a {if $a.id} id="action-button-{$a.id}"{/if} class="button {$a.class}" title="{$__globals->ACTIONS[$a.title]|default:$a.title}" href="{url for=$a.href|default:$a.title}">
							<span>{$__globals->ACTIONS[$a.title]|default:$a.title}</span>
						</a>
					{/if}
				{/foreach}
			{/if}
			<div class="cleaner">&nbsp;</div>
		</div>
		{$listing}
	</div>
	
	<div class="cleaner">&nbsp;</div>
{/block}
