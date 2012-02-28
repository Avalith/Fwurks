{extend file='scaffolding/form.tpl'}

{block name='content' prepend=true}
	{capture name="form"}
	
	<div class="main_form">
		<div class="col w2">
			<div class="inner">
				{html_field type=text name=first_name value=$data->first_name}
				{html_field type=text name=last_name value=$data->last_name}

				{html_field type='text' name='email' 	value=$data->email}

				<br /><br />
				<h2>Change password</h2>
				{html_field type='text' type='password' name='password'}
				{html_field type='text' type='password' name='password_confirm' label_name='confirm'}
			</div>
		</div>
		<div class="cleaner">&nbsp;</div>
	</div>
	<div class="sidebar_form">
		<div class="inner">

			<label>Administration Groups:</label>
			<p>{foreach $data->_groups as $g}{$g->title}{if !$g@last}, {/if}{/foreach}</p>

			{html_field type='text' name='username' value=$data->username disabled=1}

			<div class="input">
				<label class="" for="id_display_name">{$__globals.DATABASE_FIELDS.display_name}:</label>
				<select id="id_display_name" name="display_name">
					<option value="{$__globals.DISPLAY_NAMES.username}" {if $data->display_name == $__globals.DISPLAY_NAMES.username} selected="selected"{/if}>{$data->username}</option>
					{if $data->first_name || $data->last_name}
						<option value="{$__globals.DISPLAY_NAMES.full_name}" {if $data->display_name == $__globals.DISPLAY_NAMES.full_name} selected="selected"{/if}>{$data->first_name} {$data->last_name}</option>
					{/if}
					<option value="{$__globals.DISPLAY_NAMES.custom}" {if $data->display_name == $__globals.DISPLAY_NAMES.custom} selected="selected"{/if}>{$__globals.DATABASE_FIELDS.custom}</option>
				</select>
			</div>
			<div id='nick_name' style="display: none;">
				{html_field type=text name=nick_name value=$data->nick_name no_label=1}
			</div>

		</div>
	</div>
	
	{/capture}
{/block}
