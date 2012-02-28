{extend file='layout.tpl'}

{block name='actions'}
	{scaffolding_tree params=$_tree}
	
	<div class="menu">
		{$tree_add}
		{foreach $_tree.actions as $a}
			{if $_tree.user_can[$a.permission]}
				<a {if $a.id} id="action-button-{$a.id}"{/if} class="button {$a.class}" title="{$__globals.ACTIONS[$a.title]|default:$a.title}" href="{$a.href|default:$a.title}">{$__globals.ACTIONS[$a.title]|default:$a.title}</a>
			{/if}
		{/foreach}
		<div class="cleaner">&nbsp;</div>
	</div>
{/block}

{block name='content'}
	{$tree}
	<div class="cleaner">&nbsp;</div>
{/block}