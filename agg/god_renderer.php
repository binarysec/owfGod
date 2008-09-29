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
		$this->wf = $wf;
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
					if($context == NULL)
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

// 			$tpl->method = "post";
// 			$tpl->action = $this->wf->linker("/god/edit/lang");
// 			
// 			$fa1 = new core_form_hidden('back_url');
// 			$fa1->value = base64_encode($_SERVER["REQUEST_URI"]);
// 			$tpl->add_element($fa1);
// 			
// 			$fa1 = new core_form_hidden('lang_full');
// 			$fa1->value = $obj->full;
// 			$tpl->add_element($fa1);

				$tpl->set("path_name", $obj->full);
				$tpl->set("dataset", $ss->renderer());
				
				$buf .= $tpl->fetch('god/tpl_lang', TRUE);
			}
		}
		
		return($buf);
	}
	
	private function get_template() {
		$buf = NULL;

		foreach($this->_core_html->managed_list as $val) {
			$data = null;

			if(file_exists($val[1]->get_file()))
				$data = file_get_contents($val[1]->get_file());
			
			/* edit template */
			$tpl = new core_form($this->wf, "god_edit_tpl");
			$tpl->method = "post";
			$tpl->action = $this->wf->linker("/god/edit/tpl");
			
			$fa1 = new core_form_hidden('back_url');
			$fa1->value = base64_encode($_SERVER["REQUEST_URI"]);
			$tpl->add_element($fa1);
			
			$fa1 = new core_form_hidden('tpl_name');
			$fa1->value = $val[0];
			$tpl->add_element($fa1);
			
			$fa1 = new core_form_textarea('text');
			$fa1->value = $data;
			$tpl->add_element($fa1);
		
			$fs1 = new core_form_submit('submit');
			$fs1->value = 'Enregistrer';
			$tpl->add_element($fs1);
		
			$tpl->tpl_name = $val[0];

			/* chargement des variables */
			$vars = $val[1]->get_vars();
			foreach($vars as $name => $value)
				if(is_string($value)) $vars[$name] = htmlentities($value);
			$tpl->tpl_values = $vars;

			$buf .= $tpl->render('god/tpl_edit', TRUE);
		}

		return($buf);
	}
	

	
	
	
}