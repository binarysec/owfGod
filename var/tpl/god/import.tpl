<div class="content-secondary">
	<div id="jqm-homeheader">
		<h1 id="jqm-logo"><img src="%{link '/data/god/kedit.png'}%" alt="%{@ 'God super administration'}%" /></h1>
		<br />
		<p>%{@ 'God super administration'}%</p>
	</div>
	
	<p class="intro"><strong>%{@ 'Import report'}%</strong></p>
	
	<a href="%{$back}%" data-role="button" data-theme="b">
		%{@ 'Go back'}%
	</a>
</div>

<div class="content-primary">
	<ul data-role="listview" data-inset="true" data-filter="false">
		%{foreach $errors as $ctx => $errs}%
			<li data-role="list-divider">
				<strong>%{$ctx}%</strong>
			</li>
			%{foreach($errs as $err)}%
				<li>
					%{$err}%
				</li>
			%{/foreach}%
		%{/foreach}%
	</ul>
</div>

