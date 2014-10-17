<?php

class wfr_god_god_main extends wf_route_request {
	
	public function __construct($wf) {
		$this->wf = $wf;
//// 		$this->a_session = $this->wf->session();
		$this->core_lang = $this->wf->core_lang();
		$this->a_admin_html = $this->wf->admin_html();
		//$this->_god_tpl = $this->wf->god_tpl();
		$this->cipher = $this->wf->core_cipher();
		
		$this->lang = $this->wf->core_lang()->get_context("admin/system/god");
		
		$this->back = $this->cipher->get_var("back");
	}
	
	public function export() {
		
		$data = array();
		
		$langs = array("lang");
		$l = $this->core_lang->get_list();
		foreach($l as $k => $v)
			$langs[] = $k;
		
		$data[] = $langs;
		
		$q = new core_db_select("god_lang_context");
		$q->order(array("context" => WF_ASC));
		$this->wf->db->query($q);
		$ctxs = $q->get_result();
		
		foreach($ctxs as $ctx_info) {
			
			$data[] = array($ctx_info["context"]);
			$ctx = $this->core_lang->get_context($ctx_info["context"]);
			$keys = $ctx->god_get_keys(array());
			
			foreach($keys as $key_info) {
				$row = array($key_info["key"]);
				
				foreach($data[0] as $lang) {
					if($lang != "lang") {
						$cobj = $this->core_lang->get_context($ctx_info["context"], $lang);
						$row[] = $cobj->ts($key_info["key"]);
					}
				}
				
				$data[] = $row;
			}
		}
		
		$csv = new core_csv($this->wf);
		$csv->load($data);
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=Traductions.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		$csv->save("php://output");
		exit(0);
	}
	
	public function import() {
		
		$errors = array();
		$file = $this->wf->get_var("csv");
		
		// todo : better error handling
		//array(5) { ["name"]=> string(11) "sctrads.csv" ["type"]=> string(8) "text/csv" ["tmp_name"]=> string(14) "/tmp/phpqlimfd" ["error"]=> int(0) ["size"]=> int(145549) } 
		
		if(file_exists($file["tmp_name"])) {
			$errors = $this->process($file["tmp_name"]);
		}
		
		if(!empty($errors)) {
			$tpl = new core_tpl($this->wf);
			$tpl->set("errors", $errors);
			$tpl->set("back", $this->back);
			$this->a_admin_html->set_backlink($this->back);
			$this->a_admin_html->set_title($this->lang->ts("Importing traduction file"));
			$this->a_admin_html->rendering($tpl->fetch("god/import"));
		}
		else
			$this->wf->redirector($this->back);
	}
	
	private function process($filename) {
		/* vars */
		$errors = array();
		$context = null;
		$context_error = false;
		
		/* langs */
		$langs = array();
		$l = $this->core_lang->get_list();
		foreach($l as $k => $v)
			$langs[] = $k;
		
		/* csv */
		$csv = new core_csv($this->wf);
		$csv->load($filename);
		
		/* process */
		foreach($csv->get_data() as $k => $v) {
			$vf = array_filter($v);
			$key = current($vf);
			
			/* only take care of lines with the first columns set */
			if($key) {
				
				/* special key configuration */
				if($key == "lang") {
					$res = array();
					foreach($v as $index => $lg) {
						if($lg != "lang") {
							$l = $this->core_lang->resolv($lg);
							if(!$l)
								throw new wf_exception($this->wf, WF_EXC_PRIVATE, "Lang ".htmlentities($lg)." does not exists");
							$res[$index] = $l["code"];
						}
					}
					$langs = $res;
				}
				
				else {
					/* if lines has more than 1 element, this if for traduction */
					if(count($vf) > 1) {
						if($context) {
							if(!$context_error) {
								while($ts = next($vf)) {
									$i = key($vf);
									if(isset($langs[$i])) {
										$cobj = $this->core_lang->get_context($context, $langs[$i]);
										
										$file = $this->wf->locate_file($cobj->file, false, "f");
										
										if($file) {
											if(is_writable($file)) {
												
												$exists = array_key_exists(base64_encode($key), $cobj->keys);
												
												if(!$exists)
													$exists = $cobj->god_get_keys("key", $key);
												
												if($exists) {
													$cobj->change(base64_encode($key), $ts);
													
													unset($cobj->wf);
													file_put_contents($file, serialize($cobj));
													$cobj->wf = $this->wf;
												}
												else {
													$this->_err($errors, htmlentities($context), "Key <i>".htmlentities($key)."</i> does not exists in that context.");
													break;
												}
											}
											else {
												$this->_err($errors, htmlentities($context), "File <i>".$file."</i> is not writable.");
												$context_error = true;
												break;
											}
										}
										else {
											$this->_err($errors, htmlentities($context), "Key <i>".htmlentities($key)."</i>, value <i>".htmlentities($ts)."</i> : file <i>".$file."</i> not found, please create file first.");
											$context_error = true;
											break;
										}
									}
									else
										$this->_err($errors, htmlentities($context), "Key <i>".htmlentities($key)."</i> has field <i>".htmlentities($ts)."</i> out of bounds.");
								};
							}
						}
						else
							$this->_err($errors, "general", "Key <i>".htmlentities($key)."</i> was not under a context.");
					}
					
					/* otherwise this is a context switch */
					else {
						$ret = current($this->core_lang->god_get("context", $key));
						
						if($ret) {
							$context = $ret["context"];
							$context_error = false;
						}
						else
							$this->_err($errors, "general", "Context <i>".htmlentities($key)."</i> was not found in the database.");
					}
				}
			}
		}
		
		return $errors;
	}
	
	private function _err(array &$errors, $title, $msg) {
		if(!isset($errors[$title]))
			$errors[$title] = array();
		$errors[$title][] = $msg;
	}
	
}
