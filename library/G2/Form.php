<?php

class G2_Form extends Mvc_Base {
	var $twig;

	function __construct($look_dirs = [] ) {
		$twig_cache = ROOT_DIR . 'cache/tables/';
		$twig_folder = __DIR__ . '/Form';

		$params = array(
			'cache' => $twig_cache,
			'auto_reload' => true,
			'autoescape' => false,
//			'debug' => true
		);
		$loader = new Twig_Loader_Filesystem($twig_folder);
		foreach($look_dirs as $dir){
			$loader->addPath($dir);
		}
		$this->twig = new Twig_Environment($loader, $params);
	}

	function render_field($field = []){
		if(is_array($field)){
			$field = (object) $field;
		}
		$field_type = $field->field_type;

		switch ($field_type) {
			case "textarea" :
				return $this->render_textarea($field);
				break;
			case "select" :
				return $this->render_select($field);
				break;
			default : // Default field type is will also work for checkbox, password
				return $this->render_text($field);
				break;
		}
	}

	function render_text($field){
		return $this->twig->render('input.twig', ['field' => $field]);
	}
	function render_select($field){
		return $this->twig->render('select.twig', ['field' => $field]);
	}
	function render_textarea($field){
		return $this->twig->render('textarea.twig', ['field' => $field]);
	}
}

