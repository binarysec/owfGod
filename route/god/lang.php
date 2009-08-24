<?php

class wfr_god_god_lang extends wf_route_request {
	private $a_admin_html;
	private $core_lang;
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * constructeur
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function __construct($wf) {
		$this->wf = $wf;
// 		$this->a_session = $this->wf->session();
		$this->core_lang = $this->wf->core_lang();
		$this->a_admin_html = $this->wf->admin_html();
	}
	
	public function show() {
		$tpl = new core_tpl($this->wf);
		$tpl->set("contexts", $this->core_lang->god_get());
		$this->a_admin_html->rendering(
			$tpl->fetch('god/lang/index')
		);
	}
	
	public function get_form() {
		$ctx = $this->wf->get_var("context");
		
		/* get context */
		$context = $this->core_lang->god_get("context", $ctx);
		if(!is_array($context[0])) 
			exit(0);
			
		/* get all keys */
		$keys = $this->core_lang->god_get_keys("context_id", $context[0]["id"]);
		$langs = $this->core_lang->get_list();
		
		/* create result */
		$res = array();
		foreach($langs as $v) {
			$cobj = $this->core_lang->get_context(
				$ctx,
				$v["code"]
			);
			
			$res[$v["code"]] = array();
			foreach($keys as $key) {
				$rk = base64_encode($key["key"]);
				$res[$v["code"]][$rk] = 
					$cobj->ts($key["key"]);
				$res["___"][$rk] = $key["key"];
			}
		}

		/* create tpl */
		$tpl = new core_tpl($this->wf);
		$tpl->set("keys", &$res);
		$tpl->set("langs", &$langs);
		$tpl->set("ctx", &$ctx);
		echo $tpl->fetch('god/lang/form');
		exit(0);
	}
	
	public function edit() {
		$ctx = $this->wf->get_var("context");
		$ts = $this->wf->get_var("ts");
		
		/* get context */
		$context = $this->core_lang->god_get("context", $ctx);
		if(!is_array($context[0])) 
			exit(0);
			
		foreach($ts as $lang => $values) {
			$cobj = $this->core_lang->get_context(
				$ctx,
				$lang
			);
		
			foreach($values as $k => $v)
				$cobj->change($k, stripslashes($v));
				
			$file = $this->wf->get_last_filename($cobj->file);
			$this->wf->create_dir($file);

			unset($cobj->wf);
			file_put_contents(
				$file,
				serialize($cobj)
			);
			$cobj->wf = $this->wf;
		}
		
		$request = $this->wf->core_request();
		$request->set_header(
			"Location", 
			$this->wf->linker("/admin/god/lang")
		);
		$request->send_headers();
		exit(0);
	}
	
	
	
}
