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
	
	function set_required($required = true) {
		parent::set_required($required);
		
		$this->set_fields_required();
		
		return $this;
	}
	
	private function set_fields_required(&$field = false){
		
		if($field) {
			$field->set_required($this->required);
			return;
		}
		
		foreach($this->fields as &$field) {
			$field->set_required($this->required);
		}
	}
	
	function add_field(G2_FormBuilder_Field $field) {
		
		$field->name = "$this->name[$field->name]";
		
		$this->set_fields_required($field);
		$this->fields[] = $field;
	}

}
