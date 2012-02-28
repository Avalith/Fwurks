{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<div class="col w23">
		<div class="inner">
			{html_field type='text' name='username' value=$data->username}
			{html_field type='text' name='email' 	value=$data->email}
			{html_field type='text' name='name' 	value=$data->name}
			
			<div class="col w2"><div class="inner">{html_field type='password' name='password'}</div></div>
			<div class="col w2">{html_field type='password' name='password_confirm'}</div>
		</div>
	</div>
	
	<div class="col w3">
		<div class="inner">
			{html_field type=checkbox_list name='AdminGroups' data=AdminGroup::get_all() value=$data->AdminGroups() many_to_many filter}
			<br />
			{html_field type='checkbox' name='active' value=$data->active|default:$settings->active_by_default}
			<div class="cleaner">&nbsp;</div>
		</div>
	</div>
{/capture}{/block}

