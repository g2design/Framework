<?php

class G2_FormBuilder_Field_Group extends G2_FormBuilder_Field {

	var $fields = [];

	public function render($return = true) {
		
		$field_string = $this->twig->render(
				'wrappers/group.twig', array_merge($this->args,['fields' => $this->fields])
		);
		if ($return) {
			return $field_string;
		} else {
			echo $field_string;
		}
		
	}

	function add_field(G2_FormBuilder_Field $field) {
		
		$field->name = "$this->name[$field->name]";
		$this->fields[] = $field;
	}

}
