{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<div class="col w2">
		<div class="inner">
			{if count($language_locales) > 1}{tabs_navigation titles=$language_locale.all selected=$language_locale.code}{/if}
			{foreach $language_locale.all as $ln}
				{tab key=$ln@key}
					{html_field type='text' name="i18n_locales_storage[title][`$ln@key`]" label_name='title' value=$data->i18n_locales_storage->title[$ln@key]}
				{/tab}
			{/foreach}
			
			<br />
			{html_field type=checkbox name=active value=$data->active|default:$settings->active_by_default}
		</div>
	</div>
	
	<div class="col w2">
		<div class="inner">
			{html_field type=checkbox_list name=admin_users data=AdminUser::find_all() value=$data->admin_users many_to_many=1 filter=1 title_field=username}
		</div>
	</div>
	
	<div class="cleaner">&nbsp;</div>
	
	<h3>Permissions</h3>
	<br />
	{admin_permissions struct=$permissions}
{/capture}{/block}
