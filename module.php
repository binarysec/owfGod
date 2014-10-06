<?php
 
class wfm_god extends wf_module {
	public function __construct($wf) {
		$this->wf = $wf;
	}
	
	public function get_name() { return("god"); }
	public function get_description()  { return("OWF Native God module"); }
	public function get_banner()  { return("god/1.3.0"); }
	public function get_version() { return("1.3.0"); }
	public function get_authors() { return("Michael VERGOZ"); }
	public function get_depends() { return(NULL); }
	
	public function get_actions() {
		return(array(

			"/admin/system/god" => array(
				WF_ROUTE_ACTION,
				"admin/system/god",
				"show",
				$this->ts("Super god management"),
				WF_ROUTE_SHOW,
				array("session:god"),
			),
			
			/* contexts */
			"/admin/system/god/context" => array(
				WF_ROUTE_ACTION,
				"god/context",
				"show",
				$this->ts("Gestion des contextes"),
				WF_ROUTE_SHOW,
				array("session:god")
			),
			"/admin/system/god/context/edit_ctx" => array(
				WF_ROUTE_ACTION,
				"god/context",
				"edit_form",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/admin/system/god/context/edit" => array(
				WF_ROUTE_ACTION,
				"god/context",
				"edit",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/admin/system/god/context/clear" => array(
				WF_ROUTE_ACTION,
				"god/context",
				"clear_context",
				"Data",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			
			/* tpl edition */
			"/admin/system/god/tpl" => array(
				WF_ROUTE_ACTION,
				"god/tpl",
				"show",
				$this->ts("Gestion des templates"),
				WF_ROUTE_SHOW,
				array("session:god")
			),
			"/admin/system/god/tpl/edit_tpl" => array(
				WF_ROUTE_ACTION,
				"god/tpl",
				"edit_tpl",
				$this->ts("Gestion des templates"),
				WF_ROUTE_HIDE,
				array("session:god")
			),
			"/admin/system/god/tpl/edit" => array(
				WF_ROUTE_ACTION,
				"god/tpl",
				"edit",
				"",
				WF_ROUTE_HIDE,
				array("session:god")
			),
			
			/* import from csv */
			"/admin/system/god/import" => array(
				WF_ROUTE_ACTION,
				"god/main",
				"import",
				"",
				WF_ROUTE_HIDE,
				array("session:god")
			),
		));
	}
	
	public function json_module() {
 
		/* list of definitions */
		$return = array(
			array(
				/* aggregator authorized */
				"agg" => "core_lang",
	
				/* method of the authorized aggregator */
				"method" => "json_context",
	
				/* permission associated */
				"perm" => array("session:god")
			),
			
			array(
				/* aggregator authorized */
				"agg" => "god_tpl",
	
				/* method of the authorized aggregator */
				"method" => "json_context",
	
				/* permission associated */
				"perm" => array("session:god")
			),
		);

 
		/* then return the information */
		return($return);
	}
	
	public function search_module() {
		$return = array();
		
		$info = array(
			"name" => $this->ts("god_tpl"),
			"agg" => "god_tpl",
			"met_db" => "search_db",
			"met_link" => "search_link",
		);
		$return[] = $info;
		
		return($return);
	}
	
}

