<?php

class wfr_god_god_context extends wf_route_request {
	private $a_admin_html;
	private $core_lang;
	private $lang;
	private $cipher;

	private $error = false;
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
		$this->a_admin_html->rendering($tpl->fetch('god/context/index'));
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
		if(empty($context)) {
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
		$file_missing = false;
		
		foreach($langs as $v) {
			$checked = '';
			
			if($v["code"] == $language)
				$checked = ' checked="checked" ';

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
			
			/* check if file exists */
			$cobj = $this->core_lang->get_context(
				$context[0]["context"],
				$v["code"]
			);
			$file = $this->wf->locate_file($cobj->file, false, "f");
			if($file)
				$inputs .= "<span class='lang-inputs lang-selector-".$v["code"]."' >".$this->lang->ts("File located at ")."<strong>$file</strong></span>";
			else
				$file_missing = true;
			
			/*Create inputs for all langs*/
			$res[$v["code"]] = array();
			foreach($keys as $key) {
				if(!empty($key)) {
					$rk = base64_encode($key["key"]);
					$tmp = html_entity_decode($cobj->ts($key["key"]), ENT_COMPAT, $v["encoding"]);
					$res[$v["code"]][$rk] = htmlentities($tmp, ENT_COMPAT, $v["encoding"]);
					// at the end just keep this : $res[$v["code"]][$rk] = htmlentities($cobj->ts($key["key"]), ENT_COMPAT, $v["encoding"]);
					
					/*Create inputs for language edition*/
					if(!empty($res[$v["code"]][$rk])) {
						$inputs .=
							'<span class="lang-inputs lang-selector-'.$v["code"].'" >'.
								'<input type="text" '.
								'name="ts['.$v["code"].']['.$rk.']" '.
								'value="'.$res[$v["code"]][$rk].'" />'.
							'</span>';
					}
				}
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
		$tpl->set("error", $this->error);
		if($file_missing)
			$tpl->set("modules", array_reverse($this->wf->modules, true));
		
		$this->a_admin_html->set_backlink($this->back);
		$this->a_admin_html->set_title($this->lang->ts("Context Edition"));
		$this->a_admin_html->rendering($tpl->fetch('god/context/form'));
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
				$cobj->change($k, $v);
				//$cobj->change($k, htmlentities($v, ENT_COMPAT, $lselect["encoding"]));
				
			
			$file = $this->wf->locate_file($cobj->file, false, "f");
			
			if(!$file) {
				$module = $this->wf->get_var("module");
				if(isset($this->wf->modules[$module]))
					$file = $this->wf->modules[$module][0].'/'.$cobj->file;
			}
			
			if(!$file) 
				$file = $this->wf->get_last_filename($cobj->file);
			
			try {
				$this->wf->create_dir($file);
			}
			catch(wf_exception $e) {
				$this->error = $file;
				$this->edit_form();
			}

			unset($cobj->wf);
			file_put_contents(
				$file,
				serialize($cobj)
			);
			$cobj->wf = $this->wf;
		}
		
		$this->wf->redirector($this->back);
	}

	/* Route used to remove every contexts and keys from Database*/
	public function clear_context() {
		$this->core_lang->god_clear_keys();
		$this->wf->redirector($this->back);
	}
	
}