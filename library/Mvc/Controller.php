<?php

class Mvc_Controller extends Mvc_Base {

	public function index(){
		echo 'Package Not set up correctly';
	}

	/**
	 *
	 * @param type $name
	 * @return Mvc_Model
	 */
	public function loadModel($name)
	{
		require($this->get_package_dir(true) .'models/'. strtolower($name) .'.php');

		$model = new $name;
		return $model;
	}

	public function _404(){
		http_response_code(404);
		echo "
			<h1>404 page does not exist</h1>
		";

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