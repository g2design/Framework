<?php

class G2_FormBuilder_Field_Select extends G2_FormBuilder_Field {
	private $options;
	
	function render($return = true) {
		$field_string = $this->twig->render('fields/select.twig', array_merge($this->args,['options' => $this->options]));
		if($return){
			return $field_string;
		} else {
			echo $field_string;
		}
	}
	
	function add_option($label, $value) {
		if($value == false) {
			$value = $label;
		}
		
		$this->options[] = ['label' => $label, 'value'=> $value];
		return $this;
	}
	
}
