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
		<li>
			<a href="%{link '/admin/system/god/export'}%?back=%{$back}%" data-transition="fade" data-role="button" data-theme="a" data-ajax="false">
				%{@ 'Export'}%
			</a>
		</li>
		<li>
			<a href="#god-import-csv" data-rel="popup" data-position-to="window" data-role="button" data-inline="true" data-theme="a" data-transition="pop">
				%{@ 'Import'}%
			</a>
		</li>
	</ul>
	
	<form id="ctx-clearer" action="%{link '/admin/system/god/context/clear'}%?back=%{$back}%" method="POST">
		<button type="submit" data-theme="f">%{@ 'Clear Database !'}%</button>
	</form>
	
</div>

<div class="content-primary">
	<ul data-role="listview" data-inset="true" data-filter="true">
		%{foreach $contexts as $k => $v}%
			%{if $v['divider'] == true}%
				<li data-role="list-divider">
					<strong>%{$v['context']}%</strong>
				</li>
			%{else}%
				<li>
					<a href="%{link '/admin/system/god/context/edit_ctx'}%?context=%{$v['id']}%&back=%{$back}%"><strong>  %{$v['context']}% </strong></a>
				</li>
			%{/if}%
		%{/foreach}%
	</ul>
</div>


<div data-role="popup" id="god-import-csv" data-theme="a" class="ui-corner-all">

	<div data-role="header" data-theme="a" class="ui-corner-top">
		<h1>%{@ "Importing traductions"}%</h1>
	</div>
	
	<div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
		<form action="%{link '/admin/system/god/import'}%?back=%{$back}%" method="post" data-ajax="false" enctype="multipart/form-data">
			<h3 class="ui-title">%{@ "Please select your .csv file to import"}%</h3>
			<label for="god-import-file" class="ui-hidden-accessible">
				%{@ "CSV file :"}%
			</label>
			<input id="god-import-file" name="csv" type="file" data-theme="b" />
			
			<p>
				%{@ "This sould be the file format :"}%
				<table border="1" style="width: 100%;border-collapse: collapse;">
					<tr><th>Context</th></tr>
					<tr><td>Key</td><td>Traduction 1</td><td>Traduction 2</td><td>Traduction 3</td></tr>
					<tr><td>Key</td><td>Traduction 1</td><td>Traduction 2</td><td>Traduction 3</td></tr>
				</table>
			</p>
			
			<button type="submit" data-inline="true" data-theme="b" data-icon="check">Import</button>
			<a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c">Cancel</a>
		</form>
	</div>
</div>
