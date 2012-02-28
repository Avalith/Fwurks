{extend file='layout.tpl'}

{block name='content'}
	<form method="post">
		{include file='_shared/errors.tpl'}
		<div class="col w2">
			<h2>{$__globals.FORM_ADD_TITLES.listings}</h2>
			<div class="col w2"><div class="inner">{html_field type='text' name='records_per_page' value=$admin_settings->records_per_page helper=$__globals.HELPERS.records_per_page}</div></div>
			<div class="cleaner">&nbsp;</div>
			<div class="col w2"><div class="inner">{html_field type='checkbox' name='edit_link_titles' value=$admin_settings->edit_link_titles helper=$__globals.HELPERS.edit_link_titles}</div></div>
			<div class="cleaner">&nbsp;</div>
			<div class="col w2"><div class="inner">{html_field type='checkbox' name='active_by_default' value=$admin_settings->active_by_default helper=$__globals.HELPERS.active_by_default}</div></div>
			<div class="cleaner">&nbsp;</div>
			<div class="col w2"><div class="inner">{html_field type='text' name='google_analytics' value=$admin_settings->google_analytics helper=$__globals.HELPERS.google_analytics}</div></div>
			<div class="cleaner">&nbsp;</div>
		</div>
		<div class="cleaner">&nbsp;</div>
		<div class="buttons">
			{html_field type='submit' name='save' class='save'}
			{html_field type='cancel' name='cancel' class='cancel'}
			<div class="cleaner">&nbsp;</div>
		</div>
	</form>
{/block}