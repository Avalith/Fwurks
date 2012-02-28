{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<div class="main_form">
		<div class="inner">
			{html_field type='text'		name='title' 			label_name='title' 			value=$data->title}
			{html_field type='textarea'	name='description'		label_name='description'	value=$data->description}
			{html_field type='rte' 		name='content' 			label_name='content' 		value=$data->content}
	
		</div>
	</div>
	<div class="sidebar_form">
		<div class="inner">
			{html_field type='text'			name='slug'			value=$data->slug}
			{html_field type='text'			name='slug'			value=$data->slug}
			{html_field type='text'			name='slug'			value=$data->slug}
			{html_field type='text'			name='slug'			value=$data->slug}
			{html_field type='text'			name='slug'			value=$data->slug}
			{html_field type='checkbox' 	name='navigation' 	value=$data->navigation|default:1 class='pages'}
			{html_field type='tree'			name='parent_id'	value=$data->parent_id	data=$pages show_empty_option=1}
			{html_field type='text'			name='keywords'		value=$data->keywords}
			{html_field type='checkbox' 	name='active' 		value=$data->active}
		</div>			
	</div>
{/capture}{/block}
