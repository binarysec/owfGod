<script type="text/javascript">
	$(function(){
		$(".lang-inputs").hide();
		$(".lang-selector-%{$language}%").show();
		$(".lang-selector").click(function(){
			$(".lang-inputs").hide();
			$("."+this.id).show();
		});
	});
</script>

<div class="content-secondary">
	<div id="jqm-homeheader">
		<h1 id="jqm-logo"><img src="%{link '/data/god/locale.png'}%" alt="%{@ 'God super administration'}%" /></h1>
		<p>%{@ 'God super administration'}%</p>
	</div>
</div>

<div class="content-primary">
	<div data-role="fieldcontain">
		<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
			%{$lang_menu}%
		</fieldset>
	</div>
	<form id="ctx-editor" action="%{link '/admin/system/god/lang/edit'}%?context=%{$ctx}%" method="POST">
		%{$inputs}%
		<button type="submit" data-theme="b">%{@ 'Save all !'}%</button>
	</form>
</div>
