<?php
class G2_Template_Helper extends Mvc_Base{
	
	var $template = null;
	var $vars  = array();
	var $package_dir = null;
	var $package_uri = null;
	
	public function loadView($name)
	{
		$view = new Mvc_View($this->package_dir,$this->package_uri,$name);
		return $view;
	}
	
	function __construct($package_dir,$package_uri) {
		$this->package_dir = $package_dir;
		$this->package_uri = $package_uri;
	}
	
	public function get_view_package_dir() {
		return $this->package_dir;
	}
	
	public function get_view_package_uri() {
		return $this->package_uri;
	}
	
	function set_template($template){
		if(file_exists($this->package_dir."views/templates/{$template}_template.php")){
			$this->template = "templates/{$template}_template";
		}
		
		
	}
	
	function set($key,$value){
		$this->vars[$key] = $value;
	}
	
	function render($page){
		/* @var $page View */
		$page = $this->loadView("pages/$page");
		$template = $this->loadView($this->template);
		
		foreach($this->vars as $key => $value){
			$page->set($key,$value);
			$template->set($key,$value);
		}
		
		$template->set('page', $page);
		$template->render();
	}
	
	
	function render_view(Mvc_View $page){
		$template = $this->loadView($this->template);
		
		foreach($this->vars as $key => $value){
			$page->set($key,$value);
			$template->set($key,$value);
		}
		
		$template->set('page', $page);
		$template->render();
	}
	
	function get_render_view(Mvc_View $page){
		$template = $this->loadView($this->template);
		
		foreach($this->vars as $key => $value){
			$page->set($key,$value);
			$template->set($key,$value);
		}
		
		$template->set('page', $page);
		return $template->get_render();
	}
	
	public function render_template($return = false){
		/* @var $page View */
		$template = $this->loadView($this->template);
		
		foreach($this->vars as $key => $value){
			$template->set($key,$value);
		}
		
		if(!$return)
			$template->render();
		else 
			return $template->get_render();
	}
}