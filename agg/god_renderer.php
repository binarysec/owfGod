<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Web Framework 1                                       *
 * BinarySEC (c) (2000-2008) / www.binarysec.com         *
 * Author: Michael Vergoz <mv@binarysec.com>             *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~         *
 *  Avertissement : ce logiciel est protégé par la       *
 *  loi du copyright et par les traités internationaux.  *
 *  Toute personne ne respectant pas ces dispositions    *
 *  se rendra coupable du délit de contrefaçon et sera   *
 *  passible des sanctions pénales prévues par la loi.   *
 *  Il est notamment strictement interdit de décompiler, *
 *  désassembler ce logiciel ou de procèder à des        *
 *  opération de "reverse engineering".                  *
 *                                                       *
 *  Warning : this software product is protected by      *
 *  copyright law and international copyright treaties   *
 *  as well as other intellectual property laws and      *
 *  treaties. Is is strictly forbidden to reverse        *
 *  engineer, decompile or disassemble this software     *
 *  product.                                             *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

class god_renderer extends wf_agg {
	private $_core_lang;
	
	public function loader($wf) {
		$this->_core_html = $wf->core_html();
		$this->_core_lang = $wf->core_lang();
		
		/* just load forms */
		$this->wf->autoloader("core_form");
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 * Permet de recuperer le contenu managé
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function get_content() {
		$tpl = new core_tpl($this->wf);
		$tpl->set('tpl_edit', $this->get_template());
		$tpl->set('tpl_lang', $this->get_lang());
		
		return($tpl->fetch('god/body', TRUE));
	}

	
	private function get_lang() {
		$buf = NULL;
		
		$list_lang = $this->_core_lang->get_list();

		foreach($this->_core_lang->contexts as $path => $obj) {
			if(count($obj->keys) > 0) {
				$ss = new core_spreadsheet(array(
					"class" => "god_lang_table"
				));
				$ss->allow_head();
				
				$row = 0;
				$col = 0;
				
				$ss->set(
					$row,
					$col,
					"Base key"
				);
				
				$tpl = new core_tpl($this->wf);
				
				/* there are keys into the master device lets 
				   draw the first col */
				$row = 1;
				foreach($obj->keys as $v) {
					$ss->set(
						$row,
						$col,
						$v
					);
					$row++;
				}
				$col++;
				
				foreach($list_lang as $lang => $on) {
					$context = $this->_core_lang->get_context(
						$obj->full,
						$lang
					);

					if(!$context)
						$context = &$obj;
				
					/* reading keys */
					$row = 0;
					$linfo = $this->_core_lang->resolv($lang);
					$ss->set(
						$row,
						$col,
						$linfo["name"]
					);
				
					$row = 1;
					foreach($obj->keys as $key => $v) {
						$cft = new core_form_text(0);
						$cft->name = "lang[$lang][$key]";
						if($context->get($key))
							$cft->value = $context->get($key);
						else
							$cft->value = $v;
						$cft->size = 25;

						$ss->set(
							$row,
							$col,
							$cft->render()
						);
						$row++;
					}
					$col++;
				}

				$tpl->set("action", $this->wf->linker("/god/edit/lang"));
				$tpl->set("path_name", $obj->full);
				$tpl->set("backurl", base64_encode($_SERVER["REQUEST_URI"]));

				$tpl->set("dataset", $ss->renderer());
				
				$buf .= $tpl->fetch('god/lang_edit', TRUE);
			}
		}
		
		return($buf);
	}
	
	private function get_template() {
		$list = array();
		foreach($this->_core_html->managed_list as $val) {
			$backurl = base64_encode($_SERVER["REQUEST_URI"]);
			$link = $this->wf->linker("/god/form/tpl/$val[0]?backurl=$backurl");
			$list[] = array(
				$val[0],
				$link
			);
		}
		$tpl = new core_tpl($this->wf);
		$tpl->set("list", $list);
		$buf = $tpl->fetch('god/tpl_edit', TRUE);

		return($buf);
	}
	

	
	
	
}