<script type="text/javascript">
	$(function(){
		$(".tpl-textareas").hide();
		$(".tpl-selector-%{$lng}%").show();
		
		$(".tpl-selector").click(function(){
			$(".tpl-textareas").hide();
			$("."+this.id).show();
			$("textarea").keyup(); //Textarea autogrow
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
	<form id="tpl-editor" action="%{link '/admin/system/god/tpl/edit'}%?context=%{$ctx}%&back=%{$back}%" method="POST">
		%{if($error)}%
			<div style="color: red;">%{@ "Permission issue to write file "}%<strong>%{$error}%</strong></div>
		%{/if}%
		%{$textareas}%
		%{if(isset($modules))}%
			<div data-role="fieldcontain">
				<label for="ctx-editor-module">%{@ "Save in this module :"}%</label>
				<select id="ctx-editor-module" data-mini="true" data-native-menu="false" name="module">
					%{foreach($modules as $name => $module)}%
						<option value='%{$name}%'>%{$name}%</option>
					%{/foreach}%
				</select>
			</div>
		%{/if}%
		<button type="submit" data-theme="b">%{@ 'Save all !'}%</button>
	</form>
</div>
