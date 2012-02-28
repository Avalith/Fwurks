{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<div class="col w2">
		<div class="inner">
			{if count($language_locales) > 1}{tabs_navigation titles=$language_locale->all selected=$language_locale->code}{/if}
			{foreach $language_locale->all as $ln}
				{tab key=$ln@key}
					{html_field type='text' name="locales_storage[`$language_locales[$ln@key]->i18n`][title]" label_name='title' value=$data->locales_storage->{$language_locales[$ln@key]->i18n}->title}
				{/tab}
			{/foreach}
			
			<br />
			{html_field type=checkbox name=active value=$data->active|default:$settings->active_by_default}
		</div>
	</div>
	
	<div class="col w2">
		<div class="inner">
			{html_field type=checkbox_list name=admin_users data=AdminUser::get_all() value=$data->admin_users many_to_many filter title_field=username}
		</div>
	</div>
	
	<div class="cleaner">&nbsp;</div>
	
	<h3>Permissions</h3>
	<br />
	{admin_permissions struct=$permissions}
{/capture}{/block}
