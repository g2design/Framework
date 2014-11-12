<?php

class G2_FormBuilder_Field extends Mvc_Base {
	var $name, $classes;
	protected $value;
	protected $args;

	/**
	 * The twig Env to use to render the outputs
	 * @var Twig_Environment
	 */
	private $twig;

	function __construct($fieldname, $classes) {
		$this->name = $fieldname;
		$this->classes = $classes;

		$this->args = [
			'name' => &$this->name,
			'classes' => &$this->classes,
			'value' => &$this->value
		];
	}

	/**
	 * Render this form field
	 *
	 * @param type $return
	 * @return type
	 */
	function render($return = true){

		$field_string = $this->twig->render('fields/text.twig', $this->args);
		if($return){
			return $field_string;
		} else {
			echo $field_string;
		}
	}

	function set_enviroment(Twig_Environment $twig){
		$this->twig = $twig;
	}

}
