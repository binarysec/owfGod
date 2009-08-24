<?php

class wfr_god_edit extends wf_route_request {
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * constructeur
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function __construct($wf) {
		$this->wf = $wf;
		$this->a_core_request = $this->wf->core_request();
		$this->a_admin_html = $this->wf->admin_html();
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
	 * receiving lang edit form
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function edit_lang() {
		$lang = $this->wf->core_lang();
		if(!is_array($_POST["lang"]))
			$this->redirect($_POST["backurl"]);
			
		foreach($_POST["lang"] as $lk => $v) {
			$o = $lang->get_context($_POST["path_name"], $lk);
			
			foreach($v as $key => $val)
				$o->change($key, $val);
				
			$file = $this->wf->get_last_filename($o->file);
			$this->wf->create_dir($file);
			
			unset($o->wf);
			file_put_contents(
				$file,
				serialize($o)
			);
			$o->wf = $this->wf;
		}

		$this->redirect($_POST["backurl"]);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * generating template form
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function form_tpl() {
		$doc = $this->a_core_request->get_ghost();
		
		$file = $this->wf->locate_file("var/tpl".$doc.".tpl");
		if(!$file)
			$file = $this->wf->get_last_filename("var/tpl".$doc.".tpl");
			
		$data = file_get_contents($file);
		
		/* edit template */
		$tpl = new core_form($this->wf, "god_edit_tpl");
		$tpl->method = "post";
		$tpl->action = $this->wf->linker("/god/edit/tpl");
		
		$fa1 = new core_form_hidden('back_url');
		$fa1->value = $this->wf->get_var("backurl");
		$tpl->add_element($fa1);
		
		$fa1 = new core_form_hidden('tpl_name');
		$fa1->value = $doc;
		$tpl->add_element($fa1);
		
		$fa1 = new core_form_textarea('text');
		$fa1->value = $data;
		$fa1->cols = 120;
		$fa1->rows = 30;
		$tpl->add_element($fa1);
	
		$fs1 = new core_form_submit('submit');
		$fs1->value = 'Enregistrer';
		$tpl->add_element($fs1);

		$this->a_admin_html->set_title("GOD Editing template: $doc");
		$this->a_admin_html->rendering($tpl->render("god/tpl_form"));
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
