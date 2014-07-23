<?php

class Mvc_Controller extends Mvc_Base {
	
	public function index(){
		echo 'Package Not set up correctly';
	}

	public function loadModel($name)
	{
		require($this->get_package_dir() .'models/'. strtolower($name) .'.php');

		$model = new $name;
		return $model;
	}
	
	
	
	
	
	public function redirect($loc)
	{
		global $config;
		
		header('Location: '. $config['base_url'] . $loc);
	}
	
	public function __before(){
		
	}
	
	
	public function __after(){
		
	}
    
}

?>