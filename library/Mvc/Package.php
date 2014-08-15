<?php

class Mvc_Package extends Mvc_Base {
	var $name = null;
	static $instance = array();
	private $control_dir = 'controllers/';

	public function set_control_dir($dir){
		$this->control_dir = $dir.'/';
	}

	/**
	 *
	 * @return Mvc_Package
	 */
	public static function getInstance(){
		$class = get_called_class();
		if(isset(self::$instance[$class])){
			return self::$instance[$class];
		} else {
			self::$instance[$class] = new $class();
			return self::$instance[$class];
		}
	}

	function get_label() {
		return 'test';
	}

	public function route($controller = 'index', $action = 'index', $params = array()) {
		ob_start();
		$controller_obj = $this->load_controller($controller);
		if ($controller_obj) {
			if (!$this->call_action($controller_obj, $action, $params)) {

				$params = array_merge(array($action), $params);
				$action = 'index';
				$this->call_action($controller_obj, $action, $params);
			}
		} else {
			$params = array_merge(array($action), $params);
			$action = $controller;
			$controller = 'index';
			$controller_obj = $this->load_controller($controller);
			if ($controller_obj) {
				if (!$this->call_action($controller_obj, $action, $params)) {

					$params = array_merge(array($action), $params);
					$action = 'index';
					if($params[count($params)-1] == 'index'){
						$params = array_splice($params, 0, count($params) - 1);
					}
					$this->call_action($controller_obj, $action, $params);
				}
			} else {
				echo "Controller not set up Correctly ACTIOn";
			}

		}
		$content = ob_get_clean();
		return $content;
	}

	protected function load_controller($controller) {
		$controller_dir = $this->get_package_dir() . $this->control_dir;
		$controller_uri = $controller_dir . $controller . '.php';
		if (file_exists($controller_uri)) {
			require_once $controller_uri;
			$classname = ucfirst(strtolower($controller)) . '_MVC_Controller';
			@define('MVC_CONTROLLER',$controller);
			$controller = new $classname;
			return $controller;
		} else {
			return false;
		}
	}

	protected function call_action($controller_obj, $action, $params) {
		$action = strtolower($action);
		$action = str_replace('-', '_', $action);
		if (method_exists($controller_obj, $action)) {


			@define('MVC_ACTION',$action);
			@define('MVC_ROUTE',MVC_CONTROLLER.'/'.MVC_ACTION);
			/**
			 * Check for a before function
			 */
			if(method_exists($controller_obj, '__before')){
				$controller_obj->__before($params, $action);
			}
			$controller_obj->{$action}($params);
			if(method_exists($controller_obj, '__after')){
				$controller_obj->__after($params, $action);
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns Package Configuration options
	 *
	 * @return boolean|\Zend_Config_Ini
	 */
	public function get_config($file = 'package.ini'){
		$file = $this->get_package_dir().'config/'.$file;
		if(file_exists($file)){
			$config = new Zend_Config_Ini($file,  defined('APP_DEPLOY')? APP_DEPLOY : 'staging' );
			return $config;
		} else return false;
	}

	private function __construct() {

	}

	/**
	 * Special Controller Call. Only works with Contollers of type Mvc_Code_Controller
	 * @param type $name
	 * @param type $arguments
	 */
	public function __call($name, $arguments) {

		if ($controller = $this->load_controller($name)) {
			$action = array_shift($arguments);
			ob_start();
			if (!@$this->call_action($controller, $action, $arguments)) {

				$params = array_merge(array($action), $arguments);
				$action = 'index';
				if ($params[count($params) - 1] == 'index') {
					$params = array_splice($params, 0, count($params) - 1);
				}
				@$this->call_action($controller, $action, $params);
			}
			$return = ob_get_clean();
//			json_decode($return);
			if ((json_last_error() == JSON_ERROR_NONE) && false) {
				return json_decode($return);
			} else {
				return $return;
			}
		} else {
			throw new Exception('No Such Controller exists');
		}
	}
}
