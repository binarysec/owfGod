

<div class="content-secondary">
	<div id="jqm-homeheader">
		<h1 id="jqm-logo"><img src="%{link '/data/god/locale.png'}%" alt="%{@ 'God super administration'}%" /></h1>
		<p>%{@ 'God super administration'}%</p>
	</div>
	
	<p class="intro">%{@ 'Edition des contextes'}%</p>

	<ul id="button" class="localnav" data-type="horizontal" data-role="controlgroup">
		<li>
			<a id="button1" name="button" class="ui-btn-active" data-transition="fade" data-role="button" href="#">%{@ 'Languages'}%</a>
		</li>
		<li>
			<a id="button2" name="button" data-transition="fade" data-role="button" href="#">%{@ 'Templates'}%</a>
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
					<a href="%{link '/admin/system/god/lang/edit_lang'}%?context=%{$v['id']}%"><strong>  %{$v['context']}% </strong></a>
				</li>
			%{/if}%
		%{/foreach}%
	</ul>
</div>
