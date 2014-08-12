<?php

class Mvc_View extends Mvc_Base {

	private $package_dir;
	private $package_uri;
	private $pageVars = array();
	private $template;

	public function __construct($package_dir, $package_uri, $template) {
		$this->package_dir = $package_dir;
		$this->package_uri = $package_uri;
		$this->template = $this->get_view_package_dir() . '/views/' . $template . '.php';
	}

	public function exists(){
		return file_exists($this->template);
	}

	public function get_view_package_dir() {
		return $this->package_dir;
	}

	public function get_view_package_uri() {
		return $this->package_uri;
	}

	public function set($var, $val) {
		$this->pageVars[$var] = $val;
	}

	public function render() {
		extract($this->pageVars);

		ob_start();
		require($this->template);
		echo ob_get_clean();
	}

	public function get_render() {
		extract($this->pageVars);

		ob_start();
		$this->render();
		return ob_get_clean();
	}

}
