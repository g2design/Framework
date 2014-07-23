<?php

class G2_Template extends Mvc_Base {

	var $package_dir = null;
	var $package_uri = null;
	var $base_css = array();
	var $content, $css_source_dir,
			$js_source_dir, $css_source,
			$js_source, $skin_folder;
	private $file;
	private $skin_ab;
	/**
	 *
	 * @var G2_Template_Css 
	 */
	var $css;
	private $vars;

	function __construct($package_dir, $package_uri) {
		$this->package_dir = $package_dir;
		$this->package_uri = $package_uri;

		//set Default css folder
		$this->set_skin_folder();
		$this->set_template_file();
		$this->add_css_processer();
	}
	public function set_template_file($file = 'default.php'){
		$this->file = $file;
	}
	
	public function set_skin_folder($skin = 'default') {
		$this->skin_folder = $skin;
		$this->skin_ab = $this->package_dir . 'skins'.DIRECTORY_SEPARATOR. $this->skin_folder;
		// Reset css and js source;
		$this->set_css_source();
		$this->set_js_source();
	}
	
	

	function set_css_source($source = 'stylesheets') {
		$this->css_source = $source;
		
		$this->css_source_dir = $this->skin_ab.DIRECTORY_SEPARATOR . $source;
	}

	function set_js_source($source = 'javascript') {
		$this->js_source = $source;
		$this->js_source_dir = $this->skin_ab.DIRECTORY_SEPARATOR . $source;
	}

	function set_output_dir($dir = 'static/') {
		
	}

	function set_content($content) {
		$this->content = $content;
	}
	
	function render_content(){
		echo $this->content;
	}

	function render() {
		// check if template file exists inside skin folder
		$template_uri = $this->skin_ab.DIRECTORY_SEPARATOR.$this->file;
		if(file_exists($template_uri)){
			
			//Call the file to create the html output
			include $template_uri;
			
		} else {
			throw new Exception('Template File '.$template_uri.' does not exist');
		}
	}
	
	/** Adding Template special function  */
	function add_css_processer($pr = false){
		if(!$pr){
			$pr = new G2_Template_Css();
			$pr->set_base_folder($this->css_source_dir);
			$pr->set_output_dir('static/css');
		}
		
		$this->css = $pr;
	}
	
	function add_js_processer($pr = false){
		
	}
	
	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}
	
	public function __get($name) {
		if(isset($this->vars[$name]))
		return $this->vars[$name];
	}
}
