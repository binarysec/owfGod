<?php
 
class wfm_god extends wf_module {
	public function __construct($wf) {
		$this->wf = $wf;
	}
	
	public function get_name() { return("god"); }
	public function get_description()  { return("OWF Native God module"); }
	public function get_banner()  { return("god/1.2.0"); }
	public function get_version() { return("1.2.0"); }
	public function get_authors() { return("Michael VERGOZ"); }
	public function get_depends() { return(NULL); }
	
	public function get_actions() {
		return(array(
			"/admin/system/god" => array(
				WF_ROUTE_REDIRECT,
				"/admin/system/god/lang",
				$this->ts("Super god management"),
				WF_ROUTE_SHOW,
				array("session:god")
			),
			"/admin/system/god/lang" => array(
				WF_ROUTE_ACTION,
				"god/lang",
				"show",
				$this->ts("Gestion des langues"),
				WF_ROUTE_SHOW,
				array("session:god")
			),
			"/admin/system/god/lang/get_form" => array(
				WF_ROUTE_ACTION,
				"god/lang",
				"get_form",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/admin/system/god/lang/edit" => array(
				WF_ROUTE_ACTION,
				"god/lang",
				"edit",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			
			
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
