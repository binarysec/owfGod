<?php


class wfr_god_god_tpl extends wf_route_request {
	private $a_admin_html;
	private $core_lang;
	private $lang;
	private $cipher;
	public $ctx;
	
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
		$this->cipher = $this->wf->core_cipher();

		$this->ctx = $this->wf->get_var("context");

		$this->lang = $this->wf->core_lang()->get_context(
			"admin/system/god"
		);
		
		$this->back = $this->cipher->get_var("back");
		
	}
	
	public function show() {

		$res = $this->_god_tpl->search();
		
		for($a=0; $a<count($res); $a++) {
			$v = &$res[$a];
			
// 			$cobj = $this->get_context(
// 				$v["context"]
// 			);
// 		
// 			$file = $this->wf->locate_file($cobj->file);
// 			if(!$file) 
// 				$file = $this->wf->get_last_filename($cobj->file);
// 				
// 			$v["file"] = $file;
		}
// 		
		$modules_array = array();
		foreach ($res as $k => $v){
			$module = explode('/', $v['fetch']);
			if($module[0] == "")
				$module[0] = "/";
			if(!array_key_exists($module[0], $modules_array)){
				$modules_array[$module[0]] = true;
				$res[] = array(
					"fetch" => $module[0],
					"divider" => true,
				);
			}
			$res[$k]["divider"] = false;
		}
		usort($res, array($this, "cmp"));

		$tpl = new core_tpl($this->wf);
		$tpl->set("templates", $res);

		$here = $this->cipher->encode($_SERVER['REQUEST_URI']);
		$tpl->set("back", $here);
		$tpl->set("oldback", $this->wf->get_var("back"));
		
		$this->a_admin_html->set_backlink($this->back);
		$this->a_admin_html->set_title($this->lang->ts("Template Edition"));
		$this->a_admin_html->rendering(
			$tpl->fetch('god/tpl/index')
		);
	}

	public function cmp($a, $b) {
		return(strcmp($a["fetch"], $b["fetch"]));
	}
	
	public function edit_tpl() {
		/*If no ctx is given, redirect to previous lang list */
		if($this->ctx == NULL)
			$this->wf->redirector($this->back);

		$lng = $this->core_lang->get_code();
		
		$res = $this->_god_tpl->search(array("id" => $this->ctx));
		if(count($res) <= 0) {
			echo "<center>No data</center>";
			exit(0);
		}

		$langs = $this->core_lang->get_list();
		$lang_buttons = '';
		$textareas = '';
		
		foreach($langs as $v) {
			$checked = '';
			if($v["code"] == $lng)
				$checked = ' checked="checked" ';
			
			/*Create buttons with langs*/
			$lang_buttons .=
				'<input '.
					'type="radio" '.
					$checked.' '.
					'name="lang-selector" '.
					'class="tpl-selector" '.
					'id="tpl-selector-'.$v["code"].'" '.
					'value="'.$v["code"].'" '.
				'/>'.
				'<label for="tpl-selector-'.$v["code"].'">'.$v["name"].'</label>';

			$content = $this->tpl_content($res, $langs, $v["code"]);

			$textareas .=
				'<div data-role="fieldcontain" class="tpl-textareas tpl-selector-'.$v["code"].'">'.
					'<textarea id="god-textarea-'.$v["code"].'" name="ts['.$v["code"].'][data]" rows="30" style="width: 100%;">'.
						$content.
					'</textarea>'.
				'</div>';
		}

		$tpl = new core_tpl($this->wf);
		$tpl->set("result", $res[0]);
		$tpl->set("ctx", $this->ctx);
		$tpl->set("lng", $lng);
		$tpl->set("langs", $langs);
		$tpl->set("lang_buttons", $lang_buttons);
		$tpl->set("textareas", $textareas);
		$tpl->set("back", $this->wf->get_var("back"));
/*
		$tpl->set("tinymce", $this->wf->mod_exists("ppTinyMCE"));
*/
		
		$this->a_admin_html->set_backlink($this->back);
		$this->a_admin_html->set_title($this->lang->ts("Template Edition"));
		$this->a_admin_html->rendering(
			$tpl->fetch('god/tpl/form')
		);
	}

	private function tpl_content(&$res, $langs, $language) {	
		$info = $res[0];
		
		if(!array_key_exists($language, $langs)){
			echo "<center>Invalid language</center>";
			exit(0);
		}

		/*Find the appropriate template file*/
		$file = $this->locate($info["fetch"], $language, true);
		if(!is_file($file)){
			/*If file does not exist, find the default template*/
			$file = $this->locate($info["fetch"], NULL, true);
			if(!$file){
				echo "<center>TPL File not found</center>";
				exit(0);
			}
		}
		$this->wf->create_dir($file);
		
		$lselect = &$langs[$language];
		
		return file_exists($file) ?
			htmlentities(file_get_contents($file), ENT_COMPAT, $lselect["encoding"]) :
			null
		;
	}


	public function edit(){
		/*Redirect user if the guy has access to this function without good values*/
		if($this->ctx == NULL)
			$this->wf->redirector($this->back);
			
		$ts = $this->wf->get_var("ts");
		if(!isset($ts) || !is_array($ts))
			$this->wf->redirector($this->back);


		$res = $this->_god_tpl->search(array("id" => $this->ctx));
		if(count($res) <= 0) {
			echo "<center>No data</center>";
			exit(0);
		}

		$info = &$res[0];
		$l = $this->core_lang->get_list();
		

		foreach($ts as $lang => $data) {
			if(!array_key_exists($lang, $l)){
				$this->wf->redirector($this->back);
				exit(0);
			}
			
			/*Locate the appropriate file for putting data content */
			$file = $this->locate($info["fetch"], $lang, true);
			if(!$file){
				echo "<center>TPL File not found</center>";
				exit(0);
			}
			$this->wf->create_dir($file);
			
			file_put_contents($file, $data);
		}

		/*Edition is finished, back to Template list*/		
		header("Location: ".$this->back);
		exit(0);
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
