<form%{$form_attribs_string}%>
	%{foreach $form_hidden_elements as $id => $element}%
		%{$element->render()}%
	%{/foreach}%

	%{foreach $form_elements as $id => $element}%
		%{$element->render()}%<br />
	%{/foreach}%
</form>