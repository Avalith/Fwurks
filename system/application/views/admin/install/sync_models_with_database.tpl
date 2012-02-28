{extends file='scaffolding/form.tpl'}

{block name='content' prepend}{capture name="form"}
	<table class="listing">
		<thead>
			<tr>
				<th width="100">Model</th>
				<th>Query</th>
				<th width="20">Sync</th>
			</tr>
		</thead>
		<tbody>
			{foreach $sync as $s}
				<tr>
					<td>{$s->model}</td>
					<td style="font-size: 10px;">
						{if $s->queries}
							{if $s->queries->main}
								{$s->queries->main->type|upper} <strong>{$s->queries->main->table}</strong>
								<ul>{foreach $s->queries->main->columns_actions as $a}<li>{$a} {$s->queries->main->columns[$a@key]}</li>{/foreach}</ul>
								<ul>{foreach $s->queries->main->options as $o}<li>{$o}</li>{/foreach}</ul>
							{/if}
							
							{if $s->queries->i18n && $s->queries->main}<br />{/if}
							
							{if $s->queries->i18n}
								{$s->queries->i18n->type|upper} <strong>{$s->queries->i18n->table}</strong>
								<ul>{foreach $s->queries->i18n->columns_actions as $a}<li>{$a} {$s->queries->i18n->columns[$a@key]}</li>{/foreach}</ul>
							{/if}
						{/if}
					</td>
					<td class="toggle">
						{if $s->queries}
							{html_field type="checkbox" name="sync[`$s->model`]" value=0 no_label}
						{else}
							<strong style="color: #090;">OK</strong>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	
{/capture}{/block}

{block name='buttons'}
	<div class="buttons">
		{html_field type=submit value=Sync}
		<div class="cleaner">&nbsp;</div>
	</div>
{/block}
