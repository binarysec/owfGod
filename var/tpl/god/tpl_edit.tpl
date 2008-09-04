<div class="god_annonce">Editing : {$tpl_name}</div>

<div class="god_annonce_cadre">

<div class="god_annonce">Language translation</div>
<div class="god_annonce_cadre">
</div>

<div class="god_annonce">Content</div>
<div class="god_annonce_cadre">
<form{$form_attribs_string}>
	{foreach $form_hidden_elements as $id => $element}
		{$element->render()}
	{/foreach}

	{foreach $form_elements as $id => $element}
		{$element->render()}<br />
	{/foreach}
</form>
</div>



{if $tpl_values}
<div class="god_annonce">Known values for this template</div>
<div class="god_annonce_cadre">
<table width="100%">
	<tr>
		<td class="god_edit_tpl_value_title">Value (key)</td>
		<td class="god_edit_tpl_value_title">Description (content)</td>
	</tr>
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
</table>
</div>
{/if}

</div>
