<?php
class G2_FormBuilder_Field_Textarea extends G2_FormBuilder_Field{

	public function render($return = true) {
		$field_string = $this->twig->render('fields/textarea.twig', $this->args);
		if($return){
			return $field_string;
		} else {
			echo $field_string;
		}
	}
	
}