<?php

class MVC_Router {

	private static $instance = null;
	var $package_dirs = array();
	private $libraries = array();
	private $package_objects = array();
	private $default_package = array();
	private $db;

	function register_package_directory($dir) {
		//Clean path
		$dir = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $dir);
		$this->package_dirs = array($dir);
		$this->init_packages();
	}
	function set_default_package($package_name){
		$this->default_package = $package_name;
	}
	function get_packages_loaded(){

		return $this->package_objects;
	}
	static function getCurrentPackageDir($file) {
		if (self::$instance) {
			foreach (self::$instance->package_dirs as $dir) {
				if (strpos($file, $dir) !== false) {
					return $dir;
				}
			}
		} else {
			throw new Exception('Singleton Not instantiated');
		}
	}

	private function __construct() {
		//Register MVC Autoloader
		define('SYSTEM_DIR',dirname(__FILE__));
		spl_autoload_register(array($this, 'autoload_MVC_lib'));
		define('BASE_URL', Mvc_Functions::get_current_site_url());
		//Add Directory to include path;
		$path = dirname(__FILE__) . '/library';
		set_include_path(get_include_path().PATH_SEPARATOR.$path);

		// Add a error handler
		set_error_handler([$this,'errors'], E_ERROR);
	}

	public function errors($number,$string,$file,$line,$context){
		echo $string;
	}

	/**
	 *
	 * @return MVC_Router
	 */
	static function getInstance() {
		if (self::$instance) {
			return self::$instance;
		} else {
			self::$instance = new self();
			return self::$instance;
		}
	}

	function init_packages() {
		$packages = array();
		foreach ($this->package_dirs as $dir) {
			$packages = array_merge($packages, $this->get_dir_packages($dir));
		}

		return $packages;
	}

	private function get_dir_packages($dir) {
		if (is_dir($dir)) {
			$folders = scandir($dir);
			$packages = array();
			foreach ($folders as $folder) {
				if (in_array($folder, array('.', '..'))) {
					continue;
				}
				$package_class = ucfirst("Package_" . ucfirst($folder));
				$package_file_uri = "$dir/$folder/$folder.php";
				if (file_exists($package_file_uri)) {
					include $package_file_uri;
					$pac_obj = $this->package_init($package_class,$folder);
					$pac = array('label' => $pac_obj->get_label(), 'action' => $folder);
					$packages[] = $pac;
				}
			}

			return $packages;
		}
		return array();
	}

	/**
	 *
	 * @param type $package_class
	 * @return Mvc_Package
	 * @throws Exception
	 */
	private function package_init($package_class,$package_action) {
		$package = $package_class::getInstance();

		if (!$package instanceof Mvc_Package) {
			throw new Exception("$package_class is Not of the correct Type");
		} else {
			$package->name = $package_action;
			$this->package_objects[$package_class] = $package;
			Mvc_Main::init_package_applications($package);
			return $package;
		}
	}

	private function init_config(){
		//define site url



		if(isset($this->db)){
			$this->db->connect();
		}
	}
	public function set_database($db){
		$this->db = $db;
	}

	public function add_library($directory) {
		if (is_dir($directory)) {
			$this->libraries[] = $directory . ( Mvc_Functions::endsWith($directory, DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR);
			set_include_path(get_include_path().PATH_SEPARATOR.$directory);
		} else {
			//throw new Exception("Directory $directory does not exist");
		}
	}

	public function autoload_MVC_lib($class) {
		$path = explode('_', $class);
		list($last) = array_reverse($path);
		$filename = $last . '.php';
		$path_uri = dirname(implode('/', $path));

		foreach ($this->libraries as $lib) {
			$final_path = $lib . $path_uri . "/$filename";
			if (file_exists($final_path)){
				include $final_path;
				return;
			}
		}
		$final_path = dirname(__FILE__) . '/library/' . $path_uri . "/$filename";
		if (file_exists($final_path))
			require $final_path;
	}

	public function route($package, $request_params) {
		if(empty($this->package_objects)){
			$this->init_packages();
		}
		if(is_object($package)){
			$pkg = $package;
		} else {
			$pkg = $this->get_package_for($package);
		}
//		echo $pkg->name;exit;
		if($pkg){
//			var_dump($pkg);exit;
			$controller = (empty($request_params)) ? 'index' : array_shift($request_params);
			$action = (empty($request_params)) ? 'index' : array_shift($request_params);
			return $pkg->route($controller, $action, $request_params);
		}
		return false;
	}
	/**
	 *
	 * @param type $package_name
	 * @return Mvc_Package
	 */
	public function get_package_for($package_name){
		foreach($this->package_objects as $package){
			if($package_name == $package->name){
				return $package;
			}
		}
		return false;
	}

	public function dispatch(){
		// Get request url and script url
		$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

		// Get our url path and trim the / of the left and the right
		if($request_url != $script_url) {
			$url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
		} else {
			$url = null;
		}

		//Configure all constants database connections etc
		$this->init_config();

		//Strip getter from url
		if(strpos($url, '?') !== false) {
			$index = strpos($url, '?');
			$url = substr($url, 0, $index);
		}

		// Split the url into segments add route controller
		$segments = explode('/', $url);
		if($package = $this->get_package_for(current($segments))){
			array_shift($segments);
		} else {
			$package = $this->default_package;
		}
		echo $this->route($package, $segments);
	}

	public function setup_db($config){
		$config = new Zend_Config_Ini($config, APP_DEPLOY);
//		echo $config->database->dsn;exit;
		Mvc_Db::setup_db($config->database->dsn, $config->database->username, $config->database->password);
		Mvc_db::setup();
	}
}
