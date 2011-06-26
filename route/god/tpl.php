<?php


class wfr_god_god_tpl extends wf_route_request {
	private $a_admin_html;
	private $core_lang;
	
	private $_god_tpl;
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * constructeur
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function __construct($wf) {
		$this->wf = $wf;
// 		$this->a_session = $this->wf->session();
		$this->core_lang = $this->wf->core_lang();
		$this->a_admin_html = $this->wf->admin_html();
		$this->_god_tpl = $this->wf->god_tpl();
		
	}
	
	public function show() {
		$tpl = new core_tpl($this->wf);
		$this->a_admin_html->rendering(
			$tpl->fetch('god/tpl/index')
		);
	}
	
	public function edit() {
		$ctx = $this->wf->get_var("context");
		
		$res = $this->_god_tpl->search(array("id" => $ctx));
		if(count($res) <= 0) {
			echo "No data";
			exit(0);
		}

		$langs = $this->core_lang->get_list();

		$tpl = new core_tpl($this->wf);
		$tpl->set("result", $res[0]);
		$tpl->set("langs", $langs);
		$tpl->set("tinymce", $this->wf->mod_exists("ppTinyMCE"));
		$this->a_admin_html->rendering(
			$tpl->fetch('god/tpl/form')
		);
		
		
	}
	
	public function content() {
		$ctx = $this->wf->get_var("context");
		$type = $this->wf->get_var("type");
		$action = $this->wf->get_var("action");
		
		$res = $this->_god_tpl->search(array("id" => $ctx));
		if(count($res) <= 0) {
			echo "No data";
			exit(0);
		}
		$info = &$res[0];
		
		$l = $this->core_lang->get_list();
		
		/* need update ? */
		if($action == "update") {
			if($type == "default") {
				$file = $this->locate($info["fetch"]);
			}
			else {
				if(!array_key_exists($type, $l))
					exit(0);
					
				$lselect = &$l[$type];
				$file = $this->locate($info["fetch"], $type, true);
				$this->wf->create_dir($file);
			}
			
			$data = $this->wf->get_var("data");
			
			file_put_contents($file, $data);
			
			$locate = $this->wf->linker("/admin/system/god/tpl/edit").
				"?context=".$ctx.
				"&type=".$type
				;
			header("Location: $locate");
			exit(0);
		}
		
		/* check langs */
		if($type == "default") {
			$file = $this->locate($info["fetch"]);
			
			$lselect = $this->core_lang->get();
		}
		else {
			
			if(!array_key_exists($type, $l))
				exit(0);
			$lselect = &$l[$type];
			$file = $this->locate($info["fetch"], $type);
			if(!$file)
				exit(0);
		}
		
		echo htmlentities(file_get_contents($file), ENT_COMPAT, $lselect["encoding"]);
	}
	
	public function locate($tpl_name, $lang=null, $findbest=false) {
		if($lang) 
			$sdir = "/var/lang/tpl/$lang/";
		else
			$sdir = '/var/tpl/';
	
		$modrev = array_reverse($this->wf->modules);
		foreach($modrev as $mod => $mod_infos) {
			$tmp = $this->wf->modules[$mod][0].
				$sdir.$tpl_name.'.tpl';
			if(file_exists($tmp)) {

				return($tmp);
			}
		}
		
		if($findbest) {
			$file = $this->wf->get_last_filename($sdir.$tpl_name.'.tpl');
			return($file);
		}
		
		return(false);
	}
	
	
	
}
