<?php

class Mvc_Main {

	/**
	 *
	 * @var Twig_Environment
	 */
	static $twig;
	static $twigs = array();static $extensions;

	static function init_package_applications(Mvc_Package $package){
		//Get the package config
		$config = $package->get_config();
		if(!$config) return;
		foreach($config->application as $setting => $value ){
			$method_name = "init_$setting";
			if(method_exists(get_class(), $method_name)){
				self::$method_name($config,$package);
			}
		}
	}

	/**
	 *
	 * @param Zend_Config_Ini $config
	 */
	static function init_twig(Zend_Config_Ini $config,Mvc_Package $package){
		//Determine Location of html
		if(is_dir($config->twig->file_dir)){
			$twig_folder = $config->twig->file_dir;
			//@todo create template arc
			$loader = new Twig_Loader_Filesystem($twig_folder);
		} else {
			$twig_folder = $package->get_package_dir().$config->twig->file_dir;
			$loader = new Twig_Loader_Filesystem($twig_folder);
		}

		$twig_cache = $config->twig->cache_dir;

		$default_params = array(
			'cache' => $twig_cache,
			'auto_reload' => true,
			'autoescape' => false,
//			'debug' => true
		);
		if(isset($config->twig->params) && false ){
			$c_params = [];
			foreach($config->twig->params->toArray() as $key => $value){
				if(!$value){
					$value = false;
				}
				$c_params[$key] = $value;
			}
			$params = array_merge($default_params, $c_params);
		} else $params = $default_params;

		$twig = new Twig_Environment($loader, $params);
		self::$twigs[$package->name] = $twig;
		self::$extensions[$package->name] = $config->twig->extension ? $config->twig->extension : 'twig';
	}

	public static function getTwig(Mvc_Package $package){
		return self::$twigs[$package->name];
	}
	public static function get_twig_extension(Mvc_Package $package){
		return self::$extensions[$package->name];
	}
	/**
	 *
	 * @return Zend_Cache_Core
	 */
	public static function get_cache_object() {
		$frontendOptions = array(
			'lifeTime' => 3600 ,// cache lifetime of 15 minutes
			'automatic_serialization' => true
		);
		$backendOptions = array(
			'cache_dir' => ROOT_DIR . '/cache/Zend', // where to put the cache files
			'cache_db_complete_path' => ROOT_DIR.'/cache/cache.sqlite'
		);
		if(!is_dir($backendOptions['cache_dir'])){
			mkdir($backendOptions['cache_dir'],0777);
		}
		// Create an instance of Zend_Cache_Core
		return $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	}


}
