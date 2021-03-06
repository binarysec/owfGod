<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * This object must be loaded trought core_lang()->get_context()
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class core_lang_context {
	public $wf;
	public $full;
	public $file;
	public $lang;
	public $cid;
	
	public $keys = array();
	
	private $rewrite = FALSE;
	
	public function __construct($wf, $lang, $full, $file, $cid) {
		$this->wf = $wf;
		$this->lang = $lang;
		$this->full = $full;
		$this->file = $file;
		$this->cid = $cid;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Translation function
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function ts($text) {
		if(is_array($text)) {
			$rtext = array_shift($text);
			if(!is_string($rtext))
				return false;
			
			$rtext = $this->ts($rtext);
			return vsprintf($rtext, $text);
		}
		elseif(strlen($text) > 0) {
			$this->god_register_key($text);
			return $this->get_translation($text);
		}
		else
			return $text;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Used to write 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function change($key, $value) {
		$this->keys[$key] = $value;
		return true;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Used to read 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get($key) {
		return $this->keys[$key];
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * key translation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	private function get_translation($text) {
		$key = base64_encode($text);
		
		$ktext = &$this->keys[$key];
		if(!$ktext) {
			$ktext = $text;
			$this->rewrite = TRUE;
		}
		return $ktext;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * got get key
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function god_register_key($key) {
		/* add to database if do not exists */
		$r = current($this->god_get_keys("key", $key));
		if(!$r) {
			$insert = array(
				"create_t" => time(),
				"context_id" => $this->cid,
				"key" => $key
			);
			
			/* sinon on ajoute l'utilisateur */
			$q = new core_db_insert("god_lang_keys", $insert);
			$this->wf->db->query($q);
		}
		
		return true;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * got get key
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function god_get_keys($conds, $extra = null) {
		/* build where array */
		$where = is_array($conds) ?
			$conds : array($conds => $extra);
		$where["context_id"] = $this->cid;
		
		/* create cache line */
		$cl = "god_core_lang_key";
		foreach($where as $k => $v)
			$cl .= "_$k:$v";
		
		/* get cache */
		if($cache = $this->wf->core_cacher()->get($cl))
			return $cache;
		
		/* try query */
		$q = new core_db_select("god_lang_keys");
		$q->where($where);
		$this->wf->db->query($q);
		$res = $q->get_result();
		
		/* store cache */
		$this->wf->core_cacher()->store($cl, $res);
		
		return $res;
	}
}

class core_lang extends wf_agg {
	public $ini = NULL;
	
	public $current = NULL;
	public $available = NULL;
	
	private $_core_cacher;
	private $_core_register;
	
	public function loader($wf) {
		
		/** \todo SYSTEME DE CACHE */
		/* prend le fichier ini */
		$file = $this->wf->locate_file("var/lang.ini");

		$this->ini = parse_ini_file($file, TRUE);
		
		/* charge les langues disponibles */
		$t = explode(',', $this->wf->ini_arr["lang"]["available"]);
		foreach($t as $v)
			$this->available[$v] = $this->ini[$v];

		$this->_core_cacher = $this->wf->core_cacher();
		$this->_core_register = $this->wf->core_register();

		$this->default = $this->resolv($this->wf->ini_arr["lang"]["default"]);
		
		$struct = array(
			"id" => WF_PRI,
			"create_t" => WF_INT,
			"context" => WF_DATA,
		);
		$this->wf->db->register_zone(
			"god_lang_context", 
			"God language contexts",
			$struct
		);
		
		$struct = array(
			"id" => WF_PRI,
			"create_t" => WF_INT,
			"context_id" => WF_INT,
			"key" => WF_DATA
		);
		$this->wf->db->register_zone(
			"god_lang_keys", 
			"God language keys",
			$struct
		);
		
	}
	
	public function set($lang) {
		/* vérification si les données sont bonnes */
		$this->current = $this->resolv($lang);

		if(!$this->current)
			return(FALSE);
	
		/* vérification si disponible */
		if(!isset($this->available[$lang]))
			return(FALSE);
		
		/* passage des informations de contenu et d'encodage */
		$html = $this->wf->core_html();
		$html->set_meta_http_equiv(
			"Content-Language",
			array(
				"content" => $this->current["code"]
			)
		);
		$html->set_meta_http_equiv(
			"Content-Type",
			array(
				"content" => "text/html; charset=".$this->current["encoding"],
#				"charset" => $this->current["encoding"]
			)
		);
		
		/* set les elements par defaut */
		$request = $this->wf->core_request();
		$request->set_header(
			"Content-Language", 
			$this->current["code"]
		);
		$request->set_header(
			"Content-Type", "text/html"
		);
		
		/* force le passage de la langue */
		$this->_core_register->set_user_data(array(
			"language" => $lang
		));

		return($this->current);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get a translation context
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public $contexts = array();
	public function get_context($name, $lang=NULL, $create=TRUE) {
		if($lang == NULL)
			$lang = $this->find_lang();
			
		/* get the full context path */
		$full = "var/lang/ctx/".
			$lang.
			"/".
			$name;
		
		if(isset($this->contexts[$full]))
			return($this->contexts[$full]);
		
		/* register the module */
		$r = $this->god_get("context", $name);
		if(!isset($r[0]) || !is_array($r[0])) {
			/* input */
			$insert = array(
				"create_t" => time(),
				"context" => $name
			);
	
			/* sinon on ajoute l'utilisateur */
			$q = new core_db_insert("god_lang_context", $insert);
			$this->wf->db->query($q);
			$cid = $this->wf->db->get_last_insert_id('god_lang_context_id_seq');
		}
		else
			$cid = $r[0]["id"];
		
		/* locate file */
		$file = $this->wf->locate_file($full, false, "f");
		
		/* if file exists try to unserialize it*/
		if($file) {
			$obj = unserialize(file_get_contents(
				$file
			));
			if(is_object($obj)) {
				$obj->wf = $this->wf;
				$obj->cid = $cid;
				$this->contexts[$full] = &$obj;
				return($this->contexts[$full]);
			}
		}
		
		if($create) {
			/* if no file found create a virtual one */
			if(!$file)
				$file = $this->wf->get_last_filename($full);
			
			/* create context */
			$this->contexts[$full] = new core_lang_context(
				$this->wf,
				$lang, 
				$name, 
				$full,
				$cid
			);
		}
		else
			return(NULL);
			
		return($this->contexts[$full]);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Check if a lang has been coded into the route
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function check_lang_route($str) {
		/* change language if possible */
		if(isset($this->available[$str])) {
			$this->set($str);
			return(TRUE);
		}
	
		if(!$this->available[$this->wf->ini_arr["lang"]["default"]]) {
			throw new wf_exception(
				$this,
				WF_EXC_PRIVATE,
				"Default language does not exists"
			);
		}
			
		if(!$this->current) 
			$this->set($this->wf->ini_arr["lang"]["default"]);
		
		return(FALSE);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get current lang information
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get() {
		return($this->current);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get current code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get_code() {
		return($this->current["code"]);
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get default lang information
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get_default() {
		return($this->default);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get current code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get_default_code() {
		return($this->default["code"]);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Get the list of available languages
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get_list() {
		return($this->available);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Resolv information in relation with the language
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function resolv($lang) {
		if(!isset($this->available[$lang]) || !isset($this->ini[$lang]))
			return false;
		
		return $this->ini[$lang];
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Locate the proper language to use for the interface
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function find_lang() {
		$lang = "";
		
		/* check if user is loggued on and use his language preference */
		if(isset($this->wf->session()->session_me["lang"])) {
			$lang = $this->resolv($this->wf->session()->session_me["lang"]);
			if($lang)
				$lang = $lang["code"];
		}
		
		/* if did not found any, use from http headers */
		if(!$lang && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$languages = array();
			$accepts = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			
			/* parse each language of headers to organize them */
			foreach($accepts as $a) {
				$quality = 1;
				$final_lang = "";
				
				/* split lang & quality */
				if(strchr($a, ";")) {
					$expl = explode(";", $a);
					$a = $expl[0];
					$expl = explode("=", $expl[1]);
					$quality = $expl[1];
				}
				
				/* split langs */
				if(strchr($a, "-")) {
					$expl = explode("-", $a);
					$a = $this->resolv($expl[0]);
					$b = $this->resolv($expl[1]);
					if($a)
						$final_lang = $a["code"];
					elseif($b)
						$final_lang = $b["code"];
				}
				else {
					$a = $this->resolv($a);
					if($a)
						$final_lang = $a["code"];
				}
				
				/* add to array */
				if($final_lang && (!isset($languages[$final_lang]) || $quality > $languages[$final_lang]))
					$languages[$final_lang] = $quality;
			}
			
			/* parse languages and find the better one we have */
			arsort($languages);
			foreach($languages as $lang => $q) {
				$lang = $this->resolv($lang);
				if($lang) {
					$lang = $lang["code"];
					break;
				}
			}
		}
		
		/* finally, use current code */
		if(!$lang)
			$lang = $this->current["code"];
		
		return $lang;
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function god_get($conds=NULL, $extra=NULL) {
		if(is_array($conds))
			$where = $conds;
		else if($conds)
			$where = array($conds => $extra);
		else
			$where = array();
		
		/* create cache line */
		$cl = "god_core_lang";
		foreach($where as $k => $v)
			$cl .= "_$k:$v";
		
		/* get cache */
		if(($cache = $this->_core_cacher->get($cl)))
			return($cache);
		
		
		/* try query */
		$q = new core_db_select("god_lang_context");
		$q->where($where);
		$this->wf->db->query($q);
		$res = $q->get_result();

		/* store cache */
		$this->_core_cacher->store($cl, $res);
			
		return($res);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * got get key
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function god_get_keys($conds, $extra=NULL) {
		if(is_array($conds))
			$where = $conds;
		else
			$where = array($conds => $extra);
			
		/* create cache line */
		$cl = "god_core_lang_key";
		foreach($where as $k => $v)
			$cl .= "_$k:$v";
		
		/* get cache */
		if(($cache = $this->_core_cacher->get($cl)))
			return($cache);
			
		/* try query */
		$q = new core_db_select("god_lang_keys");
		$q->where($where);
		$this->wf->db->query($q);
		$res = $q->get_result();
		
		/* store cache */
		$this->_core_cacher->store($cl, $res);
			
		return($res);
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Clear all keys
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function god_clear_keys() {

		/* try query */
		$q = new core_db_delete("god_lang_keys", array());
		$this->wf->db->query($q);

		/* try query */
		$q = new core_db_delete("god_lang_context", array());
		$this->wf->db->query($q);
		
			
		return(TRUE);
	}
	
}

