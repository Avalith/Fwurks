{if count($language_locales) > 1}
	<div id="language_select">
		{html_field type='select' name='laguages_select' data=$language_locales value=$language_locale->code value_field='code' title_field='title' no_label=1}
	</div>
{/if}