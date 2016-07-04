<?php

if (!defined('ROOT_DIR')) {
	// Defines
	define('ROOT_DIR', getcwd() . '/');
	define('APP_DIR', ROOT_DIR . 'application');
}
if (!defined('APP_DEPLOY')) {
	define('APP_DEPLOY', 'staging');
}
if (!class_exists('MVC_Router')) {

	class MVC_Router {

		/**
		 *
		 * @var Monolog/Logger $debug
		 */
		static $debug;

		/**
		 *
		 * @var Monolog/Logger $error
		 */
		static $error;
		private static $instance = null;
		var $package_dirs = array();
		private $libraries = array();
		private $package_objects = array();
		private $loaded_packages = [];
		private $default_package = array();
		private $db;
		private $db_loaded = false;
		public $routes;

		function register_package_directory($dir) {
			//Clean path
			$dir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dir);
			$this->package_dirs = array_merge($this->package_dirs, array($dir));
			$this->init_packages();
		}

		function set_default_package($package_name) {
			$this->default_package = $package_name;
		}

		function get_packages_loaded() {

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
			define('SYSTEM_DIR', dirname(__FILE__));
			if (file_exists(SYSTEM_DIR . '/vendor/autoload.php')) {
				include_once SYSTEM_DIR . '/vendor/autoload.php';
			} else if (file_exists(ROOT_DIR . '/vendor/autoload.php')) {
				include_once ROOT_DIR . '/vendor/autoload.php';
			}

			$path = dirname(__FILE__) . '/library';
			//Register MVC Autoloader
			spl_autoload_register(array($this, 'autoload_MVC_lib'));
			spl_autoload_register(array($this, 'autoload_MVC_namespace'));
			$this->setup_db(false);
			$this->add_library($path);

			define('BASE_URL', Mvc_Functions::get_current_site_url());

			//Add Directory to include path;
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);

			// Logger interface
			$this->register_loggers();

			//Temp db setup
		}

		public function register_loggers() {

			$this->debug_logger();
			$this->error_logger();
		}

		private function debug_logger() {
			$handler = new Monolog\Handler\ChromePHPHandler(Monolog\Logger::DEBUG);
			self::$debug = new Monolog\Logger('Debug', [$handler]);
		}

		private function error_logger() {
			Monolog\ErrorHandler::register(self::$debug);

			//Create another logger for logging to file
			self::$error = new Monolog\Logger('Errors');
			self::$error->pushHandler(new \Monolog\Handler\ErrorLogHandler());
			Monolog\ErrorHandler::register(self::$error);
		}

		/**
		 *
		 * @return Monolog/Logger
		 */
		public static function debug($message) {
			if (empty(self::$debug)) {
				self::getInstance()->register_loggers();
			}
			return self::$debug->addDebug($message);
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
				if (in_array($dir, $this->loaded_packages)) {
					continue;
				}
				$this->loaded_packages[] = $dir;
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
						$pac_obj = $this->package_init($package_class, $folder);
						if (method_exists($pac_obj, 'setup_router')) {
							$pac_obj->setup_router();
						}
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
		private function package_init($package_class, $package_action) {
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

		private function init_config() {
			//define site url



			if (isset($this->db)) {
				$this->db->connect();
			}
		}

		public function set_database($db) {
			$this->db = $db;
		}

		public function add_library($directory) {
			if (is_dir($directory)) {
				$this->libraries[] = $directory . ( Mvc_Functions::endsWith($directory, DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR);
				set_include_path(get_include_path() . PATH_SEPARATOR . $directory);
			} else {
				//throw new Exception("Directory $directory does not exist");
			}
		}

		public function autoload_MVC_namespace($class) {
			$class = str_replace('_', '\\', $class);
			$parts = explode('\\', $class);

			foreach ($this->libraries as $lib) {
				$final_path = $lib . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
				if (file_exists($final_path)) {
					include $final_path;
					return;
				}
			}
		}

		public function autoload_MVC_lib($class) {

			$path = explode('_', $class);
			list($last) = array_reverse($path);
			$filename = $last . '.php';
			$path_uri = dirname(implode('/', $path));

			foreach ($this->libraries as $lib) {
				$final_path = $lib . $path_uri . "/$filename";
				if (file_exists($final_path)) {
					include $final_path;
					return;
				}
			}
			$final_path = dirname(__FILE__) . '/library/' . $path_uri . "/$filename";
			if (file_exists($final_path))
				require $final_path;
		}

		public function route($package, $request_params) {
			if (empty($this->package_objects)) {
				$this->init_packages();
			}
			if (is_object($package)) {
				$pkg = $package;
			} else {
				$pkg = $this->get_package_for($package);
			}
//		echo $pkg->name;exit;
			if ($pkg) {
//			var_dump($pkg);exit;
				//Convert to use a router object instead of a Hardcoded object

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
		public function get_package_for($package_name) {
			foreach ($this->package_objects as $package) {
				if ($package_name == $package->name) {
					return $package;
				}
			}
			return false;
		}

		public function dispatch() {
			if (!isset($_SERVER['SERVER_ADDR'])) {
				global $argv;
				$request_url = $argv[1];
//				$script_url = $request_url;
				
				print(var_export($request_url, true));
//				print(var_export($request_url, true));
			} else {
				// Get request url and script url
				$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
				$script_url = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
			}


			if (isset($_GET['MVc_GeTFILE']) && !empty($_GET['MVc_GeTFILE'])) {
				$fileserver = Mvc_Fileserver::get_instance();
				$fileserver->block_extension('php');
				if ($fileserver->exists($_GET['MVc_GeTFILE'])) {
					$fileserver->serve($_GET['MVc_GeTFILE']);
				}
			}
			unset($_GET['MVc_GeTFILE']);
			// Get our url path and trim the / of the left and the right
			if ($request_url != $script_url) {
				$url = trim(preg_replace('/' . str_replace('/', '\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
			} else {
				$url = null;
			}

			//Configure all constants database connections etc
			$this->init_config();

			//Strip getter from url
			if (strpos($url, '?') !== false) {
				$index = strpos($url, '?');
				$url = substr($url, 0, $index);
			}

			// Split the url into segments add route controller
			$segments = explode('/', $url);
			$slug = current($segments);

			// Let the packages now of the slug that is about to be routed
			foreach ($this->package_objects as $pac_obj) {
				if (method_exists($pac_obj, 'dispatching')) {
					$pac_obj->dispatching($slug);
				}
			}
			// Change the way routing works. Routing needs to use a routing object instead that will be registered by the package itself
			$route = $this->get_route_object($slug);
			if ($this->has_route($slug)) {
				array_shift($segments);
			}
			
			echo $route->route($segments);
		}

		

		function has_route($slug) {
			if (isset($this->routes[$slug])) {
				return true;
			} else
				return false;
		}

		/**
		 * Get a route object for a package
		 * 
		 * @param type $slug
		 * @return Mvc_Package_Router
		 */
		function get_route_object($slug) {
			if (isset($this->routes[$slug])) {
				return $this->routes[$slug];
			} else
				return $this->routes[$this->default_package];
		}

		static function add_route($slug, Mvc_Router_Interface $route) {
			$instance = self::getInstance();
			$instance->routes[$slug] = $route;
		}

		public function setup_db($config) {

			if ($config != false) {
				$config = new Zend_Config_Ini($config, APP_DEPLOY);
				R::addDatabase('DB1',$config->database->dsn, $config->database->username, $config->database->password);
				R::selectDatabase('DB1');
				
			} else {

				$temp_db = 'cache/db-temp.sqlite';
				if (!is_dir(dirname($temp_db))) {
					mkdir(dirname($temp_db), 0777);
				}

				Mvc_Db::setup_db('sqlite:' . ROOT_DIR . $temp_db, 'temp', 'temp');
				Mvc_Db::setup();
			}
			$this->db_loaded = true;
		}

	}

}

if (!function_exists('debug')) {

	function debug($message) {
		MVC_Router::debug($message);
	}

}