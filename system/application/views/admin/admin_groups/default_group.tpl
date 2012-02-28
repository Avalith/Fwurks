{extend file='scaffolding/form.tpl'}

{block name='content' prepend=true}{capture name="form"}
	<div class="col w2">
		{html_field type='select' name='group' data=$data->groups value='id' title='title' value=$settings->default_group}
	</div>
{/capture}{/block}
