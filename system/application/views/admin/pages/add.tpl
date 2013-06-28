{extends file='scaffolding/form.tpl'}

{block name='content' prepend}
	{capture name="form"}
		<div class="main_form">
			{if count($language_locales) > 1}{tabs_navigation titles=$language_locale.all selected=$language_locale.code}{/if}
			{foreach $language_locale.all as $ln}
				{tab key=$ln@key}
					<div class="col w2">
						<div class="inner">
							{html_field type='text'		name="i18n_locales_storage[title][`$ln@key`]"			label_name='title' 			value=$data->i18n_locales_storage->title[$ln@key] rel='validate noempty'	helper=$__globals.HELPERS.title}
						</div>
					</div>
					<div class="col w2">
						<div class="inner">
							{html_field type='text'		name="i18n_locales_storage[meta_title][`$ln@key`]"		label_name='meta_title'		value=$data->i18n_locales_storage->meta_title[$ln@key] helper=$__globals.HELPERS.metatitle}
						</div>
					</div>
					<div class="cleaner">&nbsp;</div>
					{html_field type='text'			name="i18n_locales_storage[keywords][`$ln@key`]"			label_name='meta_keywords'	value=$data->i18n_locales_storage->keywords[$ln@key] helper=$__globals.HELPERS.keywords}
					{html_field type='rte'			name="i18n_locales_storage[content][`$ln@key`]" 			label_name='content'		value=$data->i18n_locales_storage->content[$ln@key]}
				{/tab}
			{/foreach}
		</div>
		
		<div class="sidebar_form">
			<div class="inner">
				{html_field type='text'			name='slug'			value=$data->slug		helper=$__globals.HELPERS.slug}
				{html_field type='tree'			name='parent_id'	value=$data->parent_id	data=$pages show_empty_option=1 helper=$__globals.HELPERS.parent}
				{html_field type='checkbox'		name='navigation'	value=$data->navigation|default:1 class='pages' helper=$__globals.HELPERS.navigation}
				{html_field type='checkbox'		name='active'		value=$data->active		helper=$__globals.HELPERS.active}
			</div>
		</div>
	{/capture}
{/block}
