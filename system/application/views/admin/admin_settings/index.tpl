{extends file='layout.tpl'}

{block name='content'}
	<form method="post">
		{include file='_shared/errors.tpl'}
		<div>
			<h2>{$__globals->FORM_ADD_TITLES.listings}</h2>
			<div class="col w2"><div class="inner">
				{html_field type='text' name='records_per_page' value=$admin_settings->records_per_page helper}
				{html_field type='checkbox' name='edit_link_titles' value=$admin_settings->edit_link_titles helper}
			</div></div>
			
			<div class="col w2"><div class="inner">
				{html_field type='text' name='google_analytics' value=$admin_settings->google_analytics helper}
				{html_field type='checkbox' name='active_by_default' value=$admin_settings->active_by_default helper}
			</div></div>
			<div class="cleaner">&nbsp;</div>
			{if $user->is_root}
				<br />
				<h2>Technologies</h2>
				<table class="listing">
					<colgroup>		<col class="title" />							<col class="icons" />			<col class="icons" />			</colgroup>
					<thead><tr>		<th>Version</th>								<th>Current</th>				<th>Latest</th>					</tr></thead>
					<tbody>
						<tr>		<td><strong><a target="_blank" href="http://jquery.com/">JQuery (Back)</a></strong></td>							<td>{$jquery_back}</td>			<td>{$jquery_latest}</td>		</tr>
						<tr>		<td><strong><a target="_blank" href="http://jquery.com/">JQuery (Public)</a</strong></td>							<td>{$jquery_public}</td>		<td>{$jquery_latest}</td>		</tr>
						<tr>		<td><strong><a target="_blank" href="http://tinymce.moxiecode.com/download/download.php">TinyMCE</a</strong></td>	<td>{$tinymce}</td>				<td>{$tinymce_latest}</td>		</tr>
					</tbody>
				</table>
			{/if}
		</div>
		<div class="cleaner">&nbsp;</div>
		<div class="buttons">
			{html_field type='submit' name='save' class='save'}
			{html_field type='cancel' name='cancel' class='cancel'}
			{if $user->is_root}<div style="float:right;"><a href="{url for="//install/sync_models_with_database"}" class="button"><span>{$__globals->ACTIONS.sync_models}</span></a></div>{/if}
			<div class="cleaner">&nbsp;</div>
		</div>
	</form>
{/block}
