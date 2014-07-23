<?php

class G2_TwigController extends Mvc_Controller{
	var $template;
	
	public function __construct() {
		$this->template = new G2_TwigTemplate('templates/default.html.twig');
	}
	
	public function set_template($dir, $mainfile){
		$this->template = new G2_TwigTemplate($mainfile);
	}
	
	public function __before() {
		parent::__before();
		ob_start();
	}
	
	public function __after() {
		parent::__after();
		$content = ob_get_clean();
		$this->template->render($content);
	}
}
