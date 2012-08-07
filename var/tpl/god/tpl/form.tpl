<script type="text/javascript">
	$(function(){
		$(".tpl-textareas").hide();
		$(".tpl-selector-%{$lng}%").show();
		
		$(".tpl-selector").click(function(){
			$(".tpl-textareas").hide();
			$("."+this.id).show();
		});
	});
</script>

<div class="content-secondary">
	<div id="jqm-homeheader">
		<h1 id="jqm-logo"><img src="%{link '/data/god/kedit.png'}%" alt="%{@ 'God super administration'}%" /></h1>
		<br />
		<p>%{@ 'God super administration / Template Edition'}%</p>
	</div>

	<p class="intro">%{@ 'Editing template'}% <strong>%{$result['fetch']}%</strong></p>
</div>

<div class="content-primary">
	<div data-role="fieldcontain">
		<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
			%{$lang_buttons}%
		</fieldset>
	</div>
	<form id="tpl-editor" action="%{link '/admin/system/god/tpl/edit'}%?context=%{$ctx}%" method="POST">
		%{$textareas}%
		<button type="submit" data-theme="b">%{@ 'Save all !'}%</button>
	</form>
</div>
