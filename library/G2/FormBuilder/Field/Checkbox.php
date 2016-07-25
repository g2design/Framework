<?php

class G2_FormBuilder_Field_Checkbox extends G2_FormBuilder_Field {
	
	
	function render($return = true) {
		$field_string = $this->twig->render('fields/checkbox.twig', array_merge($this->args,['this' => $this]));
		if($return){
			return $field_string;
		} else {
			echo $field_string;
		}
	}
	
}