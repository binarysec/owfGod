<?php


class wfr_god_god_context extends wf_route_request {
	private $a_admin_html;
	private $core_lang;
	private $lang;
	private $cipher;

	public $back;
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * constructeur
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function __construct($wf) {
		$this->wf = $wf;
// 		$this->a_session = $this->wf->session();
		$this->core_lang = $this->wf->core_lang();
		$this->a_admin_html = $this->wf->admin_html();
		$this->cipher = $this->wf->core_cipher();
		
		$this->ctx = $this->wf->get_var("context");
		
		$this->lang = $this->wf->core_lang()->get_context(
			"admin/system/god"
		);
		$this->back = $this->cipher->get_var("back");
	}
	
	public function show() {
		$tpl = new core_tpl($this->wf);
		$res = $this->core_lang->god_get();
		$modules_array = array();
		foreach ($res as $k => $v){
			$module = explode('/', $v['context']);
			if($module[0] == "")
				$module[0] = "/";
			if(!array_key_exists($module[0], $modules_array)){
				$modules_array[$module[0]] = true;
				$res[] = array(
					"context" => $module[0],
					"divider" => true,
				);
			}
			$res[$k]["divider"] = false;
		}
		usort($res, array($this, "cmp"));
		$tpl->set("contexts", $res);
		
		$here = $this->cipher->encode($_SERVER['REQUEST_URI']);
		$tpl->set("back", $here);
		$tpl->set("oldback", $this->wf->get_var("back"));
		
		$this->a_admin_html->set_backlink($this->back);
		$this->a_admin_html->set_title($this->lang->ts("Context Edition"));
		$this->a_admin_html->rendering(
			$tpl->fetch('god/context/index')
		);
	}
	
	public function cmp($a, $b) {
		return(strcmp($a["context"], $b["context"]));
	}

	public function edit_form(){
		
		/*If no ctx is given, redirect to previous lang list */
		if($this->ctx == NULL)
			$this->wf->redirector($this->back);
			
		$language = $this->core_lang->get_code();

		/* get context */
		$context = $this->core_lang->god_get("id", $this->ctx);
		if(!is_array($context[0])) {
			echo "<center>No data</center>";
			exit(0);
		}

		/* get all keys */
		$keys = $this->core_lang->god_get_keys("context_id", $context[0]["id"]);
		$langs = $this->core_lang->get_list();

		/* create result */
		$res = array();
		$inputs = '';
		$lang_menu = '';
		
		foreach($langs as $v) {
			$checked = '';
			
			if($v["code"] == $language)
				$checked = ' checked="checked" ';
				
			$cobj = $this->core_lang->get_context(
				$context[0]["context"],
				$v["code"]
			);

			/*Create buttons with langs*/
			$lang_menu .=
				'<input '.
					'type="radio" '.$checked.' '.
					'name="lang-selector" '.
					'class="lang-selector" '.
					'id="lang-selector-'.$v["code"].'" '.
					'value="'.$v["code"].'" '.
				'/>'.
				'<label for="lang-selector-'.$v["code"].'">'.$v["name"].'</label>';

			/*Create inputs for all langs*/
			$res[$v["code"]] = array();
			foreach($keys as $key) {
				$rk = base64_encode($key["key"]);
				$res[$v["code"]][$rk] = 
					html_entity_decode($cobj->ts($key["key"]), ENT_COMPAT, $v["encoding"]);

				/*Create inputs for language edition*/
				$inputs .=
					'<input '.
						'type="text" '.
						'name="ts['.$v["code"].']['.$rk.']" '.
						'value="'.$res[$v["code"]][$rk].'" '.
						'class="lang-inputs lang-selector-'.$v["code"].'" '.
					'/>';
			}
		}
		
		/* create tpl */
		$tpl = new core_tpl($this->wf);
		$tpl->set("keys", $res);
		$tpl->set("ctx", $this->ctx);
		$tpl->set("ctx_name", $context[0]['context']);
		$tpl->set("language", $language);
		$tpl->set("lang_menu", $lang_menu);
		$tpl->set("inputs", $inputs);
		$tpl->set("back", $this->wf->get_var("back"));
		
		$this->a_admin_html->set_backlink($this->back);
		$this->a_admin_html->set_title($this->lang->ts("Context Edition"));
		$this->a_admin_html->rendering(
			$tpl->fetch('god/context/form')
		);
		exit(0);
	}
	
	public function edit() {
		if($this->ctx == NULL)
			$this->wf->redirector($this->back);
			
		$ts = $this->wf->get_var("ts");
		if(!isset($ts) || !is_array($ts))
			$this->wf->redirector($this->back);
		

		/* get context */
		$context = $this->core_lang->god_get("id", $this->ctx);
		if(!is_array($context[0])) 
			exit(0);

		$l = $this->core_lang->get_list();
		
		foreach($ts as $lang => $values) {
			$cobj = $this->core_lang->get_context(
				$context[0]["context"],
				$lang
			);
			
			$lselect = &$l[$lang];
			
			foreach($values as $k => $v)
				$cobj->change($k, htmlentities($v, ENT_COMPAT, $lselect["encoding"]));
				
			
			$file = $this->wf->locate_file($cobj->file);
			if(!$file) 
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
			$this->back
		);
		$request->send_headers();
		exit(0);
	}

	/* Route used to remove every contexts and keys from Database*/
	public function clear_context() {
		$this->core_lang->god_clear_keys();

		$request = $this->wf->core_request();
		$request->set_header(
			"Location",
			$this->back
		);
		$request->send_headers();
		exit(0);
	}
	
}