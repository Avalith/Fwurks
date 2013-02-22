{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<div class="col w2">
		<div class="inner">
			{html_field type='text' name='username' value=$data->username}
			{html_field type='text' name='email' value=$data->email}
			{html_field type='password' name='password'}
			{html_field type='password' name='password_confirm' label_name='confirm'}
		</div>
	</div>
	
	<div class="col w2">
		<div class="inner">
			{html_field type=checkbox_list name=admin_groups data=AdminGroup::find_all() value=$data->admin_groups many_to_many=1 filter=1}
			<br />
			{html_field type='checkbox' name='active' value=$data->active|default:$settings->active_by_default}
			<div class="cleaner">&nbsp;</div>
		</div>
	</div>
{/capture}{/block}
