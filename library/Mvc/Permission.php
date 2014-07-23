<?php

class Mvc_Permission {

	const PERMISSION_SES_VAR = 'permission-group';

	private static $instance = null;
	private $group = null;
	private $default_group = null;

	private function __construct() {
		if (isset($_SESSION[self::PERMISSION_SES_VAR])) {
			$group = R::findOne('group', 'name = :group', array('group' => $_SESSION[self::PERMISSION_SES_VAR]));
			$this->group = $group;
			if(!$group){
				$this->group = $this->default_group();
			}
		} else {

			$this->group = $this->default_group();
		}
	}
	
	/**
	 * 
	 * @return RedBean_OODBBean
	 */
	public function default_group() {
		$this->default_group = R::findOne('group', 'name = :group', array('group' => 'default'));
		if (!$this->default_group) {
			//Create the group
			$group = R::dispense('group');
			$group->name = 'default';

			R::store($group);
			$this->default_group = $group;
		}
		return $this->default_group;
	}
	
	/**
	 * 
	 * @return Mvc_Permission
	 */
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function has_permission($package, $controller = false, $action = false) {
		$conditions = [];
		if($package){
			$conditions[] = 'package = :package';
			$param['package'] = $package;
		}
		if($controller){
			$conditions[] = 'controller = :controller';
			$param['controller'] = $controller;
		}
		if($action){
			$conditions[] = 'action = :action';
			$param['action'] = $action;
		}
		$location = R::findOne('location',  implode(' AND ', $conditions), $param);
		
		if(!$location){
			$location = $this->create_location($package,$controller,$action);
		}
		
		return $this->check_permission($location);
	}
	
	private function check_permission($location){
		$params = array('location' => $location->id,'group_id' => $this->group->id);
		$permission = R::findOne('group_location','location_id = :location AND group_id = :group_id',$params);
		if(!$permission){
			//Add this location to allowed for this group
			$this->group->sharedLocation[] = $location;
			R::store($this->group);
			return true;
		} else {
//			var_dump($permission)
			return ($permission->block) ? false : true;
		}
	}
	
	private function create_location($package, $controller = null, $action = null){
		$location = R::dispense('location');
		$location->package = $package;
		$location->controller = $controller;
		$location->action = $action;
		
		//Connect location to default group as allowed
		$group = $this->default_group();
		R::store($location);
		
		$group->sharedLocation[] = $location;
		R::store($group);
		
		//Add allowed variable
		$permission = current($group->ownGroupLocation);
		$permission->block = null;
		
		R::store($permission);
		return $location;
	}

}
