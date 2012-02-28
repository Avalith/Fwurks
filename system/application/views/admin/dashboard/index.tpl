{extends file='layout.tpl'}

{block name='actions'}
	{if $title}<h1>{$title}</h1>{/if}
{/block}

{block name='content'}
	<div class="col w2">
		<div class="panel">
			<div class="header"><h3>Overview</h3></div>
			<div class="content rte">

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<ul>
					<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</li>
					<li>Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</li>
					<li>Nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</li>
					<li>Dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li>
				</ul>
				<p>Adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		
			</div>
		</div>
	</div>
	<div class="col w2">
		<div class="panel list">
			<div class="header"><h3>Manage users</h3></div>
			<div class="content">
				<ul>
					<li><a class="icon-text groups" href="#" title="Groups">Manage user groups</a></li>
					<li class="last"><a class="icon-text users" href="#" title="Users">Manage users</a></li>
				</ul>
			</div>
		</div>
	
		<div class="panel list">
			<div class="header"><h3>Demo panel</h3></div>
			<div class="content">
				<ul>
					<li><a href="#" title="Demo">Option 1</a></li>
					<li><a href="#" title="Demo">Option 1</a></li>
					<li><a href="#" title="Demo">Option 1</a></li>
					<li><a href="#" title="Demo">Option 1</a></li>
					<li><a href="#" title="Demo">Option 1</a></li>
					<li class="last"><a href="#" title="Demo">Option 1</a></li>
				</ul>
			</div>
		</div>
	
	</div>
	<div class="cleaner">&nbsp;</div>
{/block}
