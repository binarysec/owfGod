<script type="text/javascript">
	$(function() {
		$("#radio").buttonset();
		$( "input", "#radio" ).click(function() { 
			var id = $(this).attr('id');
			if(id == 'radio1')
				location.href = "%{link '/admin/system/god/lang'}%";
			else if(id == 'radio2')
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
				for(var a=0; a<data.result.length; a++) { 
					var ptr = data.result[a];
					var ctx_name = 'ctx_' + ptr.id;
					
					var append = 
						'<div id="' + ctx_name + '" class="god-accordion">'+
						'<h3><a href="#"><strong>' + ptr.context + '</strong> (in ' + ptr.file +
						')</a></h3>' +
						'<div></div>' +
						'</div>';
					;
					$('#god-context').append(append);
					
					$('div', '#' + ctx_name).html();
					
					$('#' + ctx_name).accordion({ 
						active: false, 
						clearStyle: true, 
						collapsible: true, 
						autoHeight: false
					});
				}
				
				$(".god-accordion").bind( "accordionchangestart", function(event, ui) {
					var ctx_name = $(this).attr('id');
					var id = ctx_name.substring(4);
					$.get("%{link '/admin/system/god/lang/get_form'}%" + '?context=' + id, function(data) {
						$('div', '#' + ctx_name).html(data);
					});

					
				});
			}
		});

	});
	
</script>

<h1><img src="%{link '/data/god/title_god_lang.png'}%"/>%{@ 'God super administration'}%</h1>

<div id="radio">
	<input type="radio" id="radio1" name="radio" checked="checked" /><label for="radio1">%{@ 'Language contexts edition'}%</label>
	<input type="radio" id="radio2" name="radio"  /><label for="radio2">%{@ 'Template contexts edition'}%</label>
</div>
<br/>
<div id="god-context">


</div>
