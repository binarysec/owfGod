%{css '/data/god/god.css'}%

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
			url: "%{link '/json/god_tpl/json_context'}%",
			dataType: 'json',
			data: {
				json: true
			},
			success: function(data) {
				for(var a=0; a<data.result.length; a++) { 
					var ptr = data.result[a];
					var ctx_name = 'ctx_' + ptr.id;
					
					var append = 
						'<div class="god-index-ui ui-widget-content ui-corner-all">'+
						'<strong><img src="%{link "/data/god/title_edit.png"}%"/>'+ptr.fetch+'</strong><br/>'+
						'<a href="%{link "/admin/system/god/tpl/edit"}%?context='+ptr.id+'">%{@ "Edit the template"}%</a>'+
						'</div>';
						
					$('#god-tpl').append(append);
					
				}
			}
		});
		

	});
	
</script>

<h1><img src="%{link '/data/god/title_god_tpl.png'}%"/>%{@ 'God super administration'}%</h1>

<div id="radio">
	<input type="radio" id="radio1" name="radio" /><label for="radio1">%{@ 'Language contexts edition'}%</label>
	<input type="radio" id="radio2" name="radio" checked="checked" /><label for="radio2">%{@ 'Template contexts edition'}%</label>
</div>
<br/>

<center>
	<div id="god-tpl">
	</div>
</center>



