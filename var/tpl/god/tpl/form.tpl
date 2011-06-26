%{css '/data/god/god.css'}%

%{if $tinymce}%
%{js '/data/js/jquery-1.5.js'}%
%{js '/data/js/jquery-ui-1.8.js'}%
%{js '/data/tinymce/jscripts/tiny_mce/jquery.tinymce.js'}%
%{/if}%

<script language="javascript">
	$(function() {
		var id = 'tabs-default';
		var type = 'default';
		var editor_simple = true;
		var initialized = false;
			
		$.get(
			"%{link '/admin/system/god/tpl/content'}%" + 
			'?context=%{$result["id"]}%&type=default', 
			function(data) {
				$("textarea", "#tabs-default").html(data);
			}
		);

		$('#tabs').tabs({
			select: function(event, ui) {
				id = $(ui.panel).attr('id');
				type = id.substr(5);
				

				$.get(
					"%{link '/admin/system/god/tpl/content'}%" + 
					'?context=%{$result["id"]}%&type=' + type, 
					function(data) {
						if(!initialized)
							$("textarea", ui.panel).html(data);
						else 
							$("textarea", ui.panel).tinymce().html(data);
						
					}
				);
			
			}
		});

		$("#god-link-back").button({
			icons: {
				primary: "ui-icon-arrowreturnthick-1-w",
			}
		})
		
		$("#god-link-back").click(function() {
			location.href = "%{link '/admin/system/god/tpl'}%";
			return false; 
		});
		
		$("#god-link-save").button({
			icons: {
				primary: "ui-icon-disk",
			}
		})
		
		$("#god-link-save").click(function() {
			var ret = confirm('%{@ "Are you sure you want to save this template ?"}%');
			if(ret == false)
				return(false);
			$("form", "#" + id).submit();
			return false; 
		});
		
		%{if $tinymce}%

			
			$("#god-link-editor").button({
				icons: {
					primary: "ui-icon-edit",
				}
			});
			

			$("#god-link-editor").click(function() {
				$("#god-link-editor").button("destroy");
				
				if(editor_simple == true) {
					$("#god-link-editor").text("%{@ 'Simple editor'}%");
					editor_simple = false;
					if(!initialized) {
						$('textarea').tinymce({
								// Location of TinyMCE script
								script_url : '%{link "/data/tinymce/jscripts/tiny_mce/tiny_mce.js"}%',

								// General options
								theme : "advanced",

								theme_advanced_toolbar_location : "top",
								theme_advanced_toolbar_align : "left",
								theme_advanced_statusbar_location : "bottom",
								theme_advanced_resizing : true,

						});

						initialized = true;
					}
					else
						$('textarea').tinymce().show();
				}
				else {
					$("#god-link-editor").text("%{@ 'Advanced editor'}%");
					$('textarea').tinymce().hide();
					editor_simple = true;
				}
				
				$("#god-link-editor").button({
					icons: {
						primary: "ui-icon-edit",
					}
				});
				
	
				return false; 
			});
		%{/if}%

	});
</script>

<h1><img src="%{link '/data/god/title_god_tpl.png'}%"/>%{@ 'Editing template %s', $result['fetch']}%</h1>

<div id="god-link">
	<button id="god-link-back">%{@ 'Back to template'}%</button>
	<button id="god-link-save">%{@ 'Save'}%</button>
	%{if $tinymce}%
	<button id="god-link-editor">%{@ 'Advanced editor'}%</button>
	%{/if}%
</div>
<hr size="1"/>

<div id="tabs">
	<ul>
		
		<li><a href="#tabs-default">%{@ 'Default'}%</a></li>
		
		%{foreach $langs as $lang}%
		<li><a href="#tabs-%{$lang['code']}%">%{$lang["name"]}%</a></li>
		%{/foreach}%

	</ul>

	<div id="tabs-default">
		<form method="POST" action="%{link '/admin/system/god/tpl/content'}%">
		<input type="hidden" name="context" value="%{$result['id']}%"/>
		<input type="hidden" name="type" value="default"/>
		<input type="hidden" name="action" value="update"/>
		<textarea id="god-textarea-default" name="data" rows="30" style="width: 100%"></textarea>
		</form>
	</div>
	
	%{foreach $langs as $lang}%
	<div id="tabs-%{$lang['code']}%">
		<form method="POST" action="%{link '/admin/system/god/tpl/content'}%">
		<input type="hidden" name="context" value="%{$result['id']}%"/>
		<input type="hidden" name="type" value="%{$lang['code']}%"/>
		<input type="hidden" name="action" value="update"/>
		<textarea id="god-textarea-%{$lang['code']}%" name="data" rows="30" style="width: 100%"></textarea>
		</form>
	</div>
	%{/foreach}%
</div>

