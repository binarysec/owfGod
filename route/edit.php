<?php

class wfr_god_edit extends wf_route_request {
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * constructeur
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function __construct($wf) {
		$this->wf = $wf;
		$this->a_core_session = $this->wf->core_session();
		$this->a_core_request = $this->wf->core_request();
		$this->a_core_html = $this->wf->core_html();
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * edition d'un template
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function edit_tpl() {
		$tpl = new core_form($this->wf, "god_edit_tpl");
		
		$fa1 = new core_form_hidden('back_url');
		$tpl->add_element($fa1);
		
		$fa1 = new core_form_hidden('tpl_name');
		$tpl->add_element($fa1);
			
		$fa1 = new core_form_textarea('text');
		$tpl->add_element($fa1);
	
		$fs1 = new core_form_submit('submit');
		$tpl->add_element($fs1);
		
		$values = $tpl->get_values();
		
		/* locate the file */
		$file = $this->wf->locate_file(
			"var/tpl/".
			$values["tpl_name"].
			".tpl"
		);
		
		if(is_writable($file)) {
			file_put_contents(
				$file,
				stripslashes($values["text"])
			);
		}

		$this->redirect($values["back_url"]);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * redirection
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	private function redirect($burl) {
		$request = $this->wf->core_request();
		$this->a_core_request->set_header(
			"Location", 
			base64_decode($burl)
		);
		$this->a_core_request->send_headers();
		exit(0);
	}
	
}
