<?php

class Package_Admin extends Mvc_Package {

	public function __construct() {
		MVC_Router::getInstance()->add_library($this->get_package_dir().'classes');

		define('ADMIN_URL',$this->get_package_uri(true));
	}

	public function get_label() {

		return str_replace('_', ' ', strtolower(__CLASS__));
	}

	public function get_action() {
		return strtolower(__CLASS__);
	}

}
