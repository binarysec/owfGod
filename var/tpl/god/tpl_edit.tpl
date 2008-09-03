<div class="god_annonce">Editing : {$tpl_name}</div>

<div class="god_annonce_cadre">
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
<div class="god_annonce">Know values for this template</div>
<div class="god_annonce_cadre">
<table width="100%">
		<tr>
			<td class="god_edit_tpl_value_title">Value (key)</td>
			<td class="god_edit_tpl_value_title">Description (content)</td>
		</tr>
	{foreach $tpl_values as $name => $v}
		<tr>
		{if is_string($v)}
			<td class="god_edit_tpl_value">{$name}</td>
			<td class="god_edit_tpl_value">{$v}</td>
		{else}
			<td class="god_edit_tpl_value">{$name}</td>
			<td class="god_edit_tpl_value">Array<br>
		{/if}
		</tr>
	{/foreach}
</table>
</div>
{/if}

<div class="god_annonce">Language translation</div>
<div class="god_annonce_cadre">
</div>

</div>
