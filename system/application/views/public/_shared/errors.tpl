{if $errors}
	<ul class="errors">
		{foreach $errors as $error}
			<li><label for="id_{$error->field_name}">{$error->label}{if $error->language.title} ({$error->language.title}){/if}</label>: {$error->message}</li>
		{/foreach}
	</ul>
{/if}
