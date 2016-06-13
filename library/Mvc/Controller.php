<?php

class Mvc_Controller extends Mvc_Base {

	public function index(){
		echo 'Package Not set up correctly';
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
		header('Location: '. BASE_URL . $loc);
	}

	public function __before(){

	}


	public function __after(){

	}

}

?>