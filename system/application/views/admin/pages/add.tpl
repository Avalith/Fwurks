{extend file='scaffolding/form.tpl'}

{block name='content' prepend=true}
	{capture name="form"}

		<div class="main_form">
			<div class="col w2">
				<div class="inner">
					{html_field type='text'		name='title' 			label_name='title' 			value=$data->title rel='validate noempty'	helper=$__globals.HELPERS.title}
				</div>			
			</div>
			<div class="col w2">
				<div class="inner">
					{html_field type='text'		name='meta_title'		label_name='meta_title' 	value=$data->title helper=$__globals.HELPERS.metatitle}
				</div>			
			</div>
			<div class="cleaner">&nbsp;</div>
			{html_field type='text'		name='keywords'			label_name='meta_keywords'	value=$data->keywords helper=$__globals.HELPERS.keywords}
			{html_field type='rte' 		name='content' 			label_name='content' 		value=$data->content}

			{* html_field type='textarea'	name='description'		label_name='description'	value=$data->description *}
		</div>
		<div class="sidebar_form">
			<div class="inner">
				{html_field type='text'			name='slug'			value=$data->slug		helper=$__globals.HELPERS.slug}
				{html_field type='tree'			name='parent_id'	value=$data->parent_id	data=$pages show_empty_option=1 helper=$__globals.HELPERS.parent}
				{html_field type='checkbox' 	name='navigation' 	value=$data->navigation|default:1 class='pages' helper=$__globals.HELPERS.navigation}
				{html_field type='checkbox' 	name='active' 		value=$data->active 	helper=$__globals.HELPERS.active}
			</div>			
		</div>
	
	{/capture}
{/block}
