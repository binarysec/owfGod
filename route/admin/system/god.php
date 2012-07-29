<?php

class wfr_god_admin_system_god extends wf_route_request {
	public function __construct($wf) {
		$this->wf = $wf;
	}

	public function show() {
		/* get primary content */
		$tpl = new core_tpl($this->wf);
		$in = array(			
		);	 
		$tpl->set_vars($in);
		$this->wf->admin_html()->renderlinks(array(
			"body" => $tpl->fetch('admin/system/god'),
		));
	}
}