<?php

class Mvc_Base {

	public function get_package_dir($file = false) {
		$reflector = new ReflectionClass(get_class($this));
		$file_uri = $reflector->getFileName();

		if ($file) {
			$t = debug_backtrace();
			$file_uri = $t[0]['file'];
			foreach($t as $row){
				$file = $row['file'].'<br>';
				if(strpos($file, MVC_Router::getCurrentPackageDir($file)) !== false){
					$file_uri = $file;
					break;
				}
			}
		}

		$path = str_replace(MVC_Router::getCurrentPackageDir($file_uri), '', $file_uri);
		$paths_arr = explode(DIRECTORY_SEPARATOR, $path);
		list($junk, $package) = $paths_arr;
		return MVC_Router::getCurrentPackageDir($file_uri) . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR;
	}
	
	public function get_package_instance($file = false) {
		$reflector = new ReflectionClass(get_class($this));
		$file_uri = $reflector->getFileName();

		if ($file) {
			$t = debug_backtrace();
			$file_uri = $t[0]['file'];
			foreach($t as $row){
				$file = $row['file'].'<br>';
				if(strpos($file, MVC_Router::getCurrentPackageDir($file)) !== false){
					$file_uri = $file;
					break;
				}
			}
		}

		$path = str_replace(MVC_Router::getCurrentPackageDir($file_uri), '', $file_uri);
		$paths_arr = explode(DIRECTORY_SEPARATOR, $path);
		list($junk, $package) = $paths_arr;
		return MVC_Router::getInstance()->get_package_for($package);
	}

	public function get_package_uri($file = false) {
		$reflector = new ReflectionClass(get_class($this));
		$file_uri = $reflector->getFileName();

		if ($file) {
			$t = debug_backtrace();
			$file_uri = $t[0]['file'];
			foreach($t as $row){
				$file = $row['file'].'<br>';
				if(strpos($file, MVC_Router::getCurrentPackageDir($file)) !== false){
					$file_uri = $file;
					break;
				}
			}
		}
		

		$path = str_replace(MVC_Router::getCurrentPackageDir($file_uri), '', $file_uri);
		$paths_arr = explode(DIRECTORY_SEPARATOR, $path);
		list($junk, $package) = $paths_arr;
		return BASE_URL . '' . "$package/";
	}

	public function loadHelper($name) {
		require($this->get_package_dir() . 'helpers/' . strtolower($name) . '.php');
		$helper = new $name;
		return $helper;
	}

	public function loadView($name) {
		$view = new Mvc_View($this->get_package_dir(), $this->get_package_uri(), $name);
		return $view;
	}

	public function loadPlugin($name) {
		require($this->get_package_dir() . 'plugins/' . strtolower($name) . '.php');
	}
	
	/**
	 *
	 * @param type $name
	 * @return Mvc_Model
	 */
	public function loadModel($name)
	{
		
		require_once($this->get_package_dir(true) .'models/'. strtolower($name) .'.php');

		$model = new $name;
		return $model;
	}

}
