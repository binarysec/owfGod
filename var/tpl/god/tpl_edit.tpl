<h1>Editing template {$tpl_name}</h1>

<form{$form_attribs_string}>
	{foreach $form_hidden_elements as $id => $element}
		{$element->render()}
	{/foreach}

	{foreach $form_elements as $id => $element}
		{$element->render()}<br />
	{/foreach}
</form>


{if $tpl_values}
<hr size="2">
<h1>Known values for this template</h1>

<table width="100%">
	<thead>
	<tr>
		<td class="god_edit_tpl_value_title">Value (key)</td>
		<td class="god_edit_tpl_value_title">Description (content)</td>
	</tr>
	</thead>
	<tbody>
	{foreach $tpl_values as $name => $v}
		<tr>
			<td class="god_edit_tpl_value">{$name} ({$v|type})</td>
		{if is_string($v) || is_numeric($v)}
			<td class="god_edit_tpl_value"><tt>{$v}</tt></td>
		{elseif is_array($v)}
			<td class="god_edit_tpl_value">
				<tt>
					{foreach $v as $var => $val}
						{$var},
					{/foreach}
				</tt>
			</td>
		{elseif is_object($v)}
			<td class="god_edit_tpl_value">Object&nbsp: {$v|class_name}</td>
		{else}
			<td class="god_edit_tpl_value"><em>Unknown type</em></td>
		{/if}
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}

