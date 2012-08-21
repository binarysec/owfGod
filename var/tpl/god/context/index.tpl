<div class="content-secondary">
	<div id="jqm-homeheader">
		<h1 id="jqm-logo"><img src="%{link '/data/god/locale.png'}%" alt="%{@ 'God super administration'}%" /></h1>
		<br />
		<p>%{@ 'God super administration'}%</p>
	</div>
	
	<p class="intro"><strong>%{@ 'Context Edition'}%</strong></p>

	<ul id="button" class="localnav" data-type="horizontal" data-role="controlgroup">
		<li>
			<a id="button1" name="button" class="ui-btn-active" data-transition="fade" data-role="button" href="%{link '/admin/system/god/context'}%?back=%{$oldback}%">%{@ 'Contexts'}%</a>
		</li>
		<li>
			<a id="button2" name="button" data-transition="fade" data-role="button" href="%{link '/admin/system/god/tpl'}%?back=%{$oldback}%">%{@ 'Templates'}%</a>
		</li>
	</ul>
	
</div>

<div class="content-primary">
	<ul data-role="listview" data-inset="true">
		%{foreach $contexts as $k => $v}%
			%{if $v['divider'] == true}%
				<li data-role="list-divider">
					<strong>%{$v['context']}%</strong>
				</li>
			%{else}%
				<li data-role="list-divider">
					<a href="%{link '/admin/system/god/context/edit_ctx'}%?context=%{$v['id']}%&back=%{$back}%"><strong>  %{$v['context']}% </strong></a>
				</li>
			%{/if}%
		%{/foreach}%
	</ul>
</div>
