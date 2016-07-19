<?php

use Form\Form;

/**
 * Class for creating forms in OOP Style
 */
class G2_FormBuilder extends Mvc_Base {

	private $twig;
	private $fields;
	private $method = 'POST';
	private $form_obj;
	public $submit_text = 'Submit';
	

	/**
	 * Creates form builder instance
	 * @param array $look_dirs
	 */
	function __construct($look_dirs = []) {
		
		if(is_string($look_dirs)) {
			$look_dirs = [$look_dirs];
		}
		
		$twig_cache = ROOT_DIR . 'cache/tables/';
		$twig_folder = __DIR__ . '/FormBuilder/templates';

		$params = array(
			'cache' => $twig_cache,
			'auto_reload' => true,
			'autoescape' => false,
//			'debug' => true
		);

		$look_dirs[] = $twig_folder;

		$loader = new Twig_Loader_Filesystem(array_shift($look_dirs));
		foreach ($look_dirs as $dir) {
			$loader->addPath($dir);
		}
		$this->twig = new Twig_Environment($loader, $params);
	}

	/**
	 *
	 *
	 * @return type
	 */
	function get_enviroment(){
		return $this->twig;
	}

	/**
	 * Adds a field for this form
	 * @param G2_FormBuilder_Field $field
	 */
	function add_field(G2_FormBuilder_Field $field) {
		$this->fields[] = $field;
	}

	/**
	 * Renders this form
	 */
	function render($return = false) {
		//Render all fields inside a form tag
		$form = $this->get_form_object();
		//return output of the form

		if ($return)
			return $form->parse();
		echo $form->parse();
	}

	public function get_string() {
		$inputs = '';
		foreach($this->fields as $field){
			/* @var $field G2_FormBuilder_Field */
			$inputs .= $field->render();
		}
		
		$form = $this->twig->render('wrappers/form.twig',['form_content' => $inputs , 'this' => $this]);
		return $form;
	}
	
	/**
	 * Return the html binder
	 * 
	 * @return Form\Form
	 */
	public function &get_form_object() {
		$string = $this->get_string();
		$concat = '';
		foreach ($this->fields as $field) {
			$concat .= "|$field->name";
		}
		$unique_name = md5($concat);
//		$this->form_obj = new G2_FormMagic($string , $unique_name);
		if(!$this->form_obj) {
			$this->form_obj = new Form($string , $unique_name);
		}
		
		return $this->form_obj;
	}

	function is_posted() {
		$form = $this->get_form_object();

		return $form->is_posted();
	}

	function is_valid() {
		$form = $this->get_form_object();

		return $form->validate();
	}
	
	function data(){
		$data = $this->get_form_object()->data();
		
		
		return $data;
	}

	/**
	 * A field factory. Returns field of type
	 * @param type $fieldname
	 * @param type $classes
	 * @param type $type
	 * @return \G2_FormBuilder_Field
	 */
	static function field_factory($fieldname, Twig_Environment $env, $classes = '', $type="text", $options = [] ){
		
		switch ($type) {
			case 'textarea' :
				$field = new G2_FormBuilder_Field_Textarea($fieldname, $classes, $options);
				break;
			case 'select' :
				$field = new G2_FormBuilder_Field_Select($fieldname, $classes, $options);
				break;
			case 'radio' :
				$field = new G2_FormBuilder_Field_Radio($fieldname, $classes, $options);
				break;
			case 'checkbox' :
			case 'password' :
			case 'text' :
			default :
				$field = new G2_FormBuilder_Field($fieldname, $classes, $options);
				$field->set_type($type);
		}
		$field->set_enviroment($env);
		
		
		return $field;
	}

	/**
	 * Creates a field for this envirment
	 * 
	 * @param type $fieldname
	 * @param type $classes
	 * @param type $type
	 * @return type
	 */
	function create_field($fieldname , $classes = "", $type = "text",$options = []){
		$field = self::field_factory($fieldname, $this->get_enviroment(), $classes, $type, $options);
		return $field;
	}

}
