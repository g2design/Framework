<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class G2_Mvc_Controller extends Mvc_Controller{
	/*
	 * @var $template Template_helper
	 */
	protected $template = null;
	
	public function __before(){
		$this->template = new G2_Template_Helper($this->get_package_dir(),$this->get_package_uri());
		$this->template->set_template('default');
		
		ob_start();
	}
	
	public function __after(){
		$content = ob_get_clean();
		
		$this->template->set('page_content',$content);
		
		$page = $this->template->render_template(true);
		echo $page;
	}
}