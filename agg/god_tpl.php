<?php

class god_tpl extends wf_agg {
	public $ini = NULL;
	
	public $current = NULL;
	public $available = NULL;
	
	private $_core_cacher;
	
	public function loader($wf) {
		$this->wf = $wf;
		

		$this->_core_cacher = $this->wf->core_cacher();
		
		$struct = array(
			"id" => WF_PRI,
			"create_t" => WF_INT,
			"fetch" => WF_VARCHAR,
		);
		$this->wf->db->register_zone(
			"god_tpl", 
			"God template contexts",
			$struct
		);
	}

	public function register($fetch) {
		$res = $this->search(array("fetch" => $fetch));
		if(count($res) > 0)
			return(true);
			
		$this->create($fetch);
	
		return(true);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	protected function create($fetch) {
		$insert = array(
			"fetch" => $fetch,
			"create_t" => time()
		);
		
		$q = new core_db_insert("god_tpl", $insert);
		$this->wf->db->query($q);
		
		return($code);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function search($by=array()) {
		$q = new core_db_select("god_tpl");
		$q->where($by);
		$this->wf->db->query($q);
		$res = $q->get_result();
		return($res);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	protected function update($by, $data=NULL) {
		$q = new core_db_update("god_tpl");
		$q->where($by);
		$q->insert($data);
		$this->wf->db->query($q);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 
	 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	protected function delete($by) {
		$q = new core_db_delete(
			"god_tpl",
			$by
		);
		$this->wf->db->query($q);
	}
	
	public function json_context() {
		$res = $this->search();
		

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
		usort($res, array($this, "cmp"));
		return($res);
		
	}
	
	public function cmp($a, $b) {
		return(strcmp($a["fetch"], $b["fetch"]));
	}
	

	
}

