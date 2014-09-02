<?php

if(!function_exists('deleteNode')){
	function deleteNode($node) {
		deleteChildren($node);
		$parent = $node->parentNode;
		$oldnode = $parent->removeChild($node);
	}

	function deleteChildren($node) {
		while (isset($node->firstChild)) {
			deleteChildren($node->firstChild);
			$node->removeChild($node->firstChild);
		}
	}
}

class G2_FormMagic {
	const FILE_URI = 'file_uri';
	const FILE_NAME = 'file_name';
	const FILE_TYPE = 'file_type';

	var $un_id = null;
	var $manual_invalidate = array();
	var $errors = array();
	var $content = null;
	var $parser = null;
	var $form = null;
	var $inputs = null;
	var $content_string = null;
	/**
	 * Find and creates index of form elements
	 *
	 * html string containing the form
	 */
	public function __construct($string) {

		$string = str_replace(array("\n","\r",'&nbsp;',"\t"),'',$string);
		$this->content_string =  preg_replace('|  +|', ' ', $string);

		//Generate a form id for this form to uniquely identify a form
		$id = md5($this->content_string);
		$this->un_id = $id;

		$doc = new DOMDocument("4.0", 'UTF-8');
		$doc->loadHTML($this->content_string);
		$this->content = $doc;

		//Modify input names to contain form id for better identification
		$xpath = new DOMXPath($this->content);
		$inputs = $xpath->query('// *[@name]');

		foreach($inputs as $input){

			$name =  $input->getAttribute('name');
			$name = "$this->un_id--$name";
			$input->setAttribute('name',$name);
		}
//		echo $doc->C14N;exit;
		$form = $xpath->query('//form');
		$this->form = $form->item(0);

	}

	/**
	 * Gets all post names and inserts it into the relevant value attribute
	 */
	public function post_to_value_fields(){
		$xpath = new DOMXPath($this->content);

		$el = $this->content->createElement("a");
		if(!empty($_POST)){
			foreach($_POST as $name => $value){
				$input = $xpath->query('//*[starts-with(@name,"'.$name.'")]');
				foreach($input as $i){
					/* @var $e DOMElement */
//					var_dump($value);
					$tag = $i->nodeName;
					switch($tag) {
						case "input" :
							$i->setAttribute('value', $value);
							break;
						case "select" :
							$this->post_select($i,$name, $value);
							break;
						case "textarea" :
							$this->post_textarea($i, $name, $value);
							break;
						default :
							/**
							 * Not handled jet fields
							 * checkbox, radiobutton,submit
							 */
							break;
					}

				}
			}
		}
	}

	public function post_select(DOMElement $i,$name, $value){
		$xpath = new DOMXPath($this->content);
		if(!is_array($value)){
			$value = array($value);
		}
		foreach($value as $val){
			$xquery = "//select[starts-with(@name,'{$name}')]//option[@value='$val']";
			$options = $xpath->query($xquery);
			foreach($options as $o){
				$o->setAttribute('selected', "selected");
			}
		}
	}
	public function post_textarea(DOMElement $i,$name, $value){

		$i->nodeValue = $value;
	}
	/**
	 * Return form data if form is valid
	 * @return array
	 */
	public function data(){
		// Form id was prepended to post name retrieve all posts that start with "form_id--"
		if(!empty($_POST) && $this->validate()){
			$data = array();
			foreach($_POST as $key => $value){
//				var_dump($key);
//				var_dump(strpos($key, "$this->un_id--"));
				if(strpos($key, "$this->un_id--") !== false){
					$key = str_replace("$this->un_id--", '', $key);
					$data[$key] = $value;
				}


			}
			/**
			$xpath = new DOMXPath($this->content);
			$inputs = $xpath->query('// *[@name]');
			*/
			return $data;
		} else return false;

	}
	function set_data($data){
		foreach($data as $key => $field){
			//encrypt $names to match with names
			$key = "$this->un_id--$key";
			$_POST[$key] = $field;
		}

		return true;
	}

	public function get_uploaded_files(){
		if($this->is_posted()){
			$xpath = new DOMXPath($this->content);
			$file_inputs = $xpath->query("// *[@type='file']");

			/* @var $input DOMElement*/
//				$name = $input->getAttribute('name');
			$files = array();
			foreach($_FILES as $field => $file_u){

				$name_cleaned = str_replace("$this->un_id--", '', $field);
				if($file_u['error'] == UPLOAD_ERR_OK){
					$file[self::FILE_URI] = $file_u['tmp_name'];
					$file[self::FILE_NAME] = $file_u['name'];
					$file[self::FILE_TYPE] = $file_u['type'];

					// attach All form data to this array
					$file = array_merge($file,$this->data());

					$files[$name_cleaned] = $file;
				}
			}

			return $files;
		} else return false;
	}

	public function invalidate($name, $message){
		$key = "$this->un_id--$name";
		$this->manual_invalidate[$key] = $message;

	}
	public function validate_input(DOMElement $i){
		$validation = $i->getAttribute('data-validation');
		$allow_empty = $i->getAttribute('data-default');
		if(empty($allow_empty) || $allow_empty == 'true'){
			$allow_empty = true;
		} else {
			$allow_empty = false;
		}

		//if true check default value
		$tag = $i->nodeName;
		$name = $i->getAttribute('name');
		$default_value = null;
		switch ($tag){
			case "input" :
				$default_value = $i->getAttribute('value');
				break;
			case "select":
				$xpath = new DOMXPath($this->content);
				$xquery = "//select[@name='$name']//option";
				$options = $xpath->query($xquery);
				$default_value = $options->item(0)->getAttribute('value');
				break;
			case "textarea":
				$default_value = $i->nodeValue;
				break;
			default :
				$default_value = null;
				break;
		}

		if(!empty($_POST)){
			$value = $_POST[$i->getAttribute('name')];
			if($validation == 'empty' && !empty($value)){ // If input has value. DO redirect sommer dadelik.
				header('Location:');
				exit;
			}


			if(isset($this->manual_invalidate[$name])){

				return $this->manual_invalidate[$name];
			}
			if(empty($validation) || ( $allow_empty && ( $allow_empty || $value == $default_value ) ) ){
				if(empty($validation) && !$allow_empty && empty($value)){
					return "Please fill in this field";
				} else
					return true;
			}
			switch ($validation){
				case "email" :
					if(filter_var($value, FILTER_VALIDATE_EMAIL)){
						return true;
					} else return "Not an valid email address";
					break;
				case "date" :

					$date = date_parse($value);
					$result = checkdate($date['month'], $date['day'], $date['year']);

					if($value == $default_value){
						return "Field must be filled in and needs to be a valid date";
					}

					if($result){
						return true;
					}
					break;
			}

			return true;
			}

		return true;
	}
	public function mailto($email, $subject, $template = false,$css = false){
		//Manipulate css to remove none showing elements
		$xpath = new DOMXPath($this->content);
		$xqry = "//input[@data-validation=\"empty\"] | //img";
		$inputs = $xpath->query($xqry);
		foreach($inputs as $i){
			/* @var $i DOMElement */
			$i->parentNode->removeChild($i);
		}

		$selects = $xpath->query('//select');
		foreach($selects as $select){
			$name = $select->getAttribute('name');
			if(!empty($_POST[$name])){
				$value = $_POST[$name];
				$option = $xpath->query('//option[@value="'.$value.'"]');
				foreach($option as $o){
					$value = $option->item(0)->nodeValue;
					break;
				}
			} else $value = '';

			$new_input = $this->content->createElement('input');
			$new_input->setAttribute('name', $name);
			$new_input->setAttribute('value', $value);
			$_POST[$name] = $value;
			$select->parentNode->replaceChild($new_input, $select);
		}



		$other = $xpath->query('//input | //select | //textarea');
		foreach($other as $i){
			$i->setAttribute('disabled', 'disabled');
		}

		$html = $this->parse();

		ob_start();
		if(!empty($template) && file_exists($template)){
			include $template;
		} else echo $html;
		$message = ob_get_clean();

//		echo $message;exit;
		if($css && file_exists($css)){
			$cssparce = new CssToInlineStyles($message, file_get_contents($css));
			$message = $cssparce->convert(false);
		}
//		echo $message;exit;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: ' .$_POST['email'] . "\r\n" .
					'Reply-To: ' .$_POST['email'] . "\r\n" .
					'X-Mailer: PHP/' . phpversion();

//		echo $message;
		mail($email, $subject, $message, $headers);
	}
	/**
	 * Function will validate fields in html an modify html with validation messages
	 *
	 */
	public function visual_validate(){
		$xpath = new DOMXPath($this->content);

		if(!empty($_POST)){
			if(!empty($_FILES)){
				foreach($_FILES as $name => $file){
					if(isset($this->manual_invalidate[$name])){
						$_POST[$name] = 'File';
					}
				}
			}
//			var_dump($_POST);exit;
			foreach($_POST as $name => $value){
				$input = $xpath->query('//*[@name="'.$name.'"]');
				foreach($input as $i){
					/*   */
					/* @var $i DOMNode */
					$tag = $i->nodeName;

					//Send node to validate_input function. This will return a message if not success
					$result = $this->validate_input($i);

					if($result !== true){ // validation error ocurred

						$message = $this->content->createElement('span', $result);
						$message->setAttribute('class', 'error small');
						$message->setAttribute('id', $name.'_validate');
						$i->setAttribute('title', $result);
						$i->setAttribute('class', $i->getAttribute('class') .' field_error');
//						 if( $i->nextSibling )
//							{
//							   $i->parentNode->insertBefore($message, $i->nextSibling);
//							}
//							else
//							{
//							   $i->parentNode->appendChild($message);
//							}
					}
				}
			}
		}
	}
	public function is_posted(){
//		echo $this->un_id;
		$name_expected = "un_id--$this->un_id";
		if(!empty($_POST) && isset($_POST[$name_expected]) && $_POST[$name_expected] == $this->un_id){
			return !empty($_POST);
		} else
			return false;
	}
	public function validate(){
		$xpath = new DOMXPath($this->content);
		$valid = true;
		if(!empty($_POST)){
			foreach($_POST as $name => $value){
				$input = $xpath->query('//*[@name="'.$name.'"]');
				foreach($input as $i){
					/*
					/* @var $i DOMNode */
					$tag = $i->nodeName;

					//Send node to validate_input function. This will return a message if not success
					$result = $this->validate_input($i);
					if($result !== true || isset($this->manual_invalidate[$name])){ // validation error ocurred
						$valid = false;
						$this->errors[] = $result;
					}
				}
			}
		}
		return $valid;
	}

	public function thank_you($message){
		$xpath = new DOMXPath($this->content);
		$xqry = "//form";

		$form = $xpath->query($xqry)->item(0);
		$element = $this->content->createElement('div',$message);
		$element->setAttribute('class', 'success');

		$form->parentNode->replaceChild($element,$form);

		$html_fragment = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $this->content->saveHTML()));
		return $html_fragment;
	}

	public function parse(){
		$this->visual_validate();
		//Add unique identifier field to form DOM
		$xpath = new DOMXPath($this->content);
		$form = $xpath->query('//form')->item(0);
		$input = $this->content->createElement('input');
		$input->setAttribute('type', 'hidden');
		$input->setAttribute('name', 'un_id--'.$this->un_id);
		$input->setAttribute('value', $this->un_id);
		$form->appendChild($input);

		$this->post_to_value_fields();

		$html_fragment = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $this->content->saveHTML()));
		return $html_fragment;
	}
}
?>