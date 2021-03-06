<?php
/**
 * Class improves fields management of table renderer
 *
 * To use class features use the function set_fields
 */
class G2_ImprovedDataTable extends G2_DataTable {

	var $fields;

	/**
	 * Sets the fields in the correct form
	 * $fields variable definition
	 * array of object containing a label and name key.
	 * name is equal to the name of the field
	 * and label is the label connected to that field
	 *
	 * @param type $fields
	 */
	function set_fields($fields) {
		$this->fields = $fields;
	}

//	public function get_resultset() {
//		return $result = parent::get_resultset();
//		// Reformat resultset to fit to fields selected
//
//		if (!empty($this->fields)) {
//			$fields = $this->get_table_fields();
//			$new_results = [];
//
//			foreach ($result as $set) {
//				$n_set = [];
//				if (is_array($set)) {
//					$set = $this->array_to_object($set);
//				}
//				foreach ($fields as $field) {
//					$n_set[$field] = $set->$field;
//				}
////				$n_set = $this->array_to_object($n_set);
//				$new_results[] = $n_set;
//			}
////			var_dump($new_results);
//			return $new_results;
//		} else
//			return $result;
//	}

	function get_headers() {
		if (empty($this->fields)) {
			return parent::get_headers();
		} else {
			return $this->get_table_fields();
		}
	}

	function get_table_fields() {

		$fields = [];
		foreach ($this->fields as $field) {
			if (is_array($field)) { // Convert array to object
				$field = $this->array_to_object($field);
			}

			if (isset($field->name)) {
				$fields[] = $field->name;
			} else {
				throw new Exception('Expect there to be keys name and label in given array');
			}
		}

		return $fields;
	}

	function render() {
		if (empty($this->fields)) {
			parent::render();
		} else {
			$twig_cache = ROOT_DIR . 'cache/tables/';
			$twig_folder = __DIR__ . '/DataTable';

			$params = array(
				'cache' => $twig_cache,
				'auto_reload' => true,
				'autoescape' => false,
//			'debug' => true
			);
			$loader = new Twig_Loader_Filesystem($twig_folder);
			$twig = new Twig_Environment($loader, $params);
//		var_dump($twig_folder);exit;
			$current_url = substr(Mvc_Functions::curPageURL(), 0, strpos(Mvc_Functions::curPageURL(), '?') !== false ? strpos(Mvc_Functions::curPageURL(), '?') : strlen(Mvc_Functions::curPageURL()));
			$copy = $_GET;
			unset($copy['p']);
			if (empty($copy)) {
				$get_con = '?';
			} else {
				$current_url .= '?' . Mvc_Functions::GET_to_string($copy);
				$get_con = '&';
			}
//			echo '<pre>';
//			var_dump($this->get_resultset());
//			echo '</pre>';
//			exit;
			return $twig->render('improved-table.twig', [
						'data' => $this->get_resultset(),
						'pages' => Mvc_Db::get_last_total_pages() != false ? Mvc_Db::get_last_total_pages() : $this->total_pages,
						'current' => Mvc_Db::get_current_page() != false ? Mvc_Db::get_current_page() : $this->current_page,
						'headers' => $this->get_headers(),
						'functions' => $this->get_functions(),
						'instance' => $this,
						'get_con' => $get_con,
						'current_url' => $current_url
			]);
		}
	}

	function get_label($fieldname) {

		foreach ($this->fields as $field) {
			if (is_array($field)) { // Convert array to object
				$field = $this->array_to_object($field);
			}

			if (isset($field->label)) {
				if( $field->name == $fieldname ){
					return $field->label;

				}
			} else {
				throw new Exception('Expect there to be keys name and label in given array');
			}
		}
	}

}
