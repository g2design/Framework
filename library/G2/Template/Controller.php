<?php
class G2_Template_Controller extends Mvc_Controller {
	/*
	 * @var $template G2_Template
	 */
	protected $template;
	protected $css;

	public function __before(){
		
//		$template = new G2_Template($this->get_package_dir(true), $this->get_package_uri(true));
//		$this->set_template($template);
		ob_start();
	}
	
	public function __after(){
		if($this->template){
			$this->template->set_content(ob_get_clean());
			$this->template->render();
		} else {
			throw new Exception('Template Not Set');
		}
	}
	
	protected function set_template(G2_Template $template){
		$this->template = $template;
		$this->css = &$template->css;
	}
}