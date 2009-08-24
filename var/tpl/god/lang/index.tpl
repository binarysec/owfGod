{css '/data/god/god.css'}

{js '/data/yui/build/connection/connection-min.js'}

{literal}
<script type="text/javascript">

	/* ajax request */
	function show_div(dst_id, ctx) {
		var dst = document.getElementById('data_' + dst_id);
		
		dst.visibility = "hidden";
		dst.innerHTML = "Loading datas...";
		
		var handleSuccess = function(o) {
			if(o.responseText !== undefined){
				dst.innerHTML = o.responseText;
			}
		}
	
		var handleFailure = function(o) {
			if(o.responseText !== undefined){
				dst.innerHTML = "Server error";
			}
		}
		
		var callback = {
			success:handleSuccess,
			failure:handleFailure
		};
		
		{/literal}
		var request = YAHOO.util.Connect.asyncRequest(
			'GET', 
			'{link '/admin/god/lang/get_form'}' + '?context=' + ctx, 
			callback
		);
		{literal}
	}
	
</script>
{/literal}

<h1><img src="{link '/data/god/title_god_lang.png'}"/>{@ 'Editing language contexts'}</h1>

<div class="dataset_data god_dataset_data">
	<table class="dataset_data_table">
		<tbody class="dataset_data_body">
			{foreach $contexts as $context}
			<tr>
				<th>
				<h1>
				<a href="javascript: show_div({$context['id']}, '{$context['context']}');">
				{$context['context']}
				</a>
				</h1>
				</th>
			</tr>
			<tr>
				<th><div id="data_{$context["id"]}"></div></th>
			</tr>
			
			{/foreach}
		</tbody>
	</table>
</div>