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
			"/god" => array(
				WF_ROUTE_ACTION,
				"god",
				"show_data",
				"Data",
				WF_ROUTE_SHOW,
				array("session:god")
			)
		));
	}
}

?>
