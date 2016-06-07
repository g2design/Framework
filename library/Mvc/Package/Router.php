<?php

class Mvc_Package_Router extends Mvc_Base implements Mvc_Router_Interface {

	var $package = null;

	public function __construct(Mvc_Package $package_instance) {
		$this->package = $package_instance;
	}

	public function route($request_params) {
		$pkg = $this->package;
		if ($pkg) {
//			var_dump($pkg);exit;
			//Convert to use a router object instead of a Hardcoded object

			$controller = (empty($request_params)) ? 'index' : array_shift($request_params);
			$action = (empty($request_params)) ? 'index' : array_shift($request_params);
			return $pkg->route($controller, $action, $request_params);
		}
		
		return false;
	}

}
