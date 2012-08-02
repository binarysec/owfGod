<script type="text/javascript">
	$(function() {
		$( "a", "#button" ).click(function() {
			var id = $(this).attr('id');
			if(id == 'button1')
				location.href = "%{link '/admin/system/god/lang'}%";
			else if(id == 'button2')
				location.href = "%{link '/admin/system/god/tpl'}%";
				
			return(false); 
		});
		
		$.ajax({
			url: "%{link '/json/core_lang/json_context'}%",
			dataType: 'json',
			data: {
				json: true
			},
			success: function(data) {
				modules_array = new Array();
				for(var a=0; a<data.result.length; a++) { 
					var ptr = data.result[a];
					var ctx_name = 'ctx_' + ptr.id;
					
					/*var append = 
						'<div id="' + ctx_name + '" class="god-accordion">'+
						'<h3><a href="#"><strong>' + ptr.context + '</strong> (in ' + ptr.file +
						')</a></h3>' +
						'<div></div>' +
						'</div>';
					;*/
					var module = ptr.context.split('/');
					if(modules_array.indexOf(module[0]) < 0){
						modules_array.push(module[0]);
						var module_section =
							'<li data-role="list-divider" id="' + module[0] + '" class="ui-bar-b">'+
								'<strong>' + module[0] + '</strong>'
							'</li>';
						$('#god-context').append(module_section);
					}
					
					var append =
						'<li data-role="list-divider" id="' + ctx_name + '" class="god-accordion">'+
							'<a href="#"><strong>' + ptr.context + '</strong></a>'
						'</li>';
					$('#god-context').append(append);
					
					$('div', '#' + ctx_name).html();
					
					/*$('#' + ctx_name).accordion({ 
						active: false, 
						clearStyle: true, 
						collapsible: true, 
						autoHeight: false
					});*/
				}

				$('#god-context').listview('refresh');
				
				/*$(".god-accordion").bind( "accordionchangestart", function(event, ui) {
					var ctx_name = $(this).attr('id');
					var id = ctx_name.substring(4);
					$.get("%{link '/admin/system/god/lang/get_form'}%" + '?context=' + id, function(data) {
						$('div', '#' + ctx_name).html(data);
					});

					
				});*/
			}
		});

	});
	
</script>

<h1><img src="%{link '/data/god/title_god_lang.png'}%"/>%{@ 'God super administration'}%</h1>

<!--<div id="radio">
	<input type="radio" id="radio1" name="radio" checked="checked" /><label for="radio1">%{@ 'Language contexts edition'}%</label>
	<input type="radio" id="radio2" name="radio"  /><label for="radio2">%{@ 'Template contexts edition'}%</label>
</div>-->
<ul id="button" class="localnav" data-type="horizontal" data-role="controlgroup">
	<li>
		<a id="button1" name="button" class="ui-btn-active" data-transition="fade" data-role="button" href="#">%{@ 'Language contexts edition'}%</a>
	</li>
	<li>
		<a id="button2" name="button" data-transition="fade" data-role="button" href="#">%{@ 'Template contexts edition'}%</a>
	</li>
</ul>

<div data-role="content">
	<ul data-role="listview" id="god-context">
	
	</ul>
</div>
