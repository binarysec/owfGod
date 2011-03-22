%{css '/data/god/god.css'}%

<script type="text/javascript">

	/* ajax request */
	function show_div(dst_id, ctx) {
		var dst = document.getElementById('data_' + dst_id);
		$.get("%{link '/admin/system/god/lang/get_form'}%" + '?context=' + ctx, function(data) {
			$(dst).html(data);
			$(dst).show('slow');
		});		
	}
	
</script>


<h1><img src="%{link '/data/god/title_god_lang.png'}%"/>%{@ 'Editing language contexts'}%</h1>

<div class="dataset_data god_dataset_data">
	<table class="dataset_data_table">
		<tbody class="dataset_data_body">
			%{foreach $contexts as $context}%
			<tr>
				<th>
				<h1>
				<a href="javascript: show_div(%{$context['id']}%, '%{$context['context']}%');">
				%{$context['context']}%
				</a>
				</h1>
				</th>
			</tr>
			<tr>
				<th><div id="data_%{$context["id"]}%"></div></th>
			</tr>
			
			%{/foreach}%
		</tbody>
	</table>
</div>