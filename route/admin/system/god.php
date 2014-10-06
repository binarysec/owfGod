<?php

class wfr_god_admin_system_god extends wf_route_request {

	private $cipher;
	public $back;
	
	public function __construct($wf) {
		$this->wf = $wf;
		$this->cipher = $this->wf->core_cipher();
		$this->back = $this->cipher->get_var("back");
	}

	public function show() {
		$tpl = new core_tpl($this->wf);
		$this->wf->admin_html()->set_backlink($this->back);
		$this->wf->admin_html()->renderlinks(array(
			"body" => $tpl->fetch('admin/system/god'),
		));
	}
}
