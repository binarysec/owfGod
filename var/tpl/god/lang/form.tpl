{if is_array($keys["___"])}
<form method="POST" action="{link '/admin/system/god/lang/edit'}">
<input type="hidden" name="context" value="{$ctx}">
<div class="dataset_data">
	<table class="dataset_data_table">
		<thead class="dataset_data_head">
			<tr>
			{foreach $langs as $lang}
			<th>
			{$lang["name"]}
			</th>
			{/foreach}
			</tr>
		</thead>
	
		<tbody class="dataset_data_body">
			{foreach $keys["___"] as $k => $kv}
			<tr>
				{foreach $langs as $lang}
				<td>
				<input 
					type="text" 
					size="30" 
					name="ts[{$lang['code']}][{$k}]" 
					value="{$keys[$lang['code']][$k]|html}"
				/>
				</td>
				{/foreach}
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
<input type="submit"/>
</form>
{else}
No data
{/if}
