<?php
 
class god extends wf_module {
	public function __construct($wf) {
		$this->wf = $wf;
	}
	
	public function get_name() { return("god"); }
	public function get_description()  { return("Web Framework God Module"); }
	public function get_banner()  { return("WFGod/1.0"); }
	public function get_version() { return("1.0"); }
	public function get_authors() { return("Michael VERGOZ"); }
	public function get_depends() { return(NULL); }
	
	public function get_actions() {
		return(array(
			"/god/form/tpl" => array(
				WF_ROUTE_ACTION,
				"edit",
				"form_tpl",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/god/edit/tpl" => array(
				WF_ROUTE_ACTION,
				"edit",
				"edit_tpl",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/god/edit/lang" => array(
				WF_ROUTE_ACTION,
				"edit",
				"edit_lang",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			)
		));
	}
}

?>
