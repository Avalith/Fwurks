{extend file='layout.tpl'}

{block name='actions'}
	<h1>{$__globals.ACTIONS[$__action]} {$__globals.FORM_ADD_TITLES[Inflector::singularize($__controller)]}</h1>
{/block}

{block name='content'}
	<form method="post" enctype="multipart/form-data">
		{include file='_shared/errors.tpl'}
		
		{$smarty.capture.form}
		
		<div class="cleaner">&nbsp;</div>
		<div class="buttons">
			{html_field type=submit name=save class=save}
			{html_field type=submit name=save_reload class=save}
			{html_field type=cancel name=cancel class=cancel}
			<div class="cleaner">&nbsp;</div>
		</div>
	</form>	
{/block}

