<?php

class Package_Submissions extends Mvc_Package {

	public function __construct() {
		MVC_Router::getInstance()->add_library($this->get_package_dir() . 'classes');
	}

	public function get_label() {

		return str_replace('_', ' ', strtolower(__CLASS__));
	}

	public function get_action() {
		return strtolower(__CLASS__);
	}



	public function get_admin_controller(){
		return 'admin';
	}

	public function get_permission(){
		return 'developer';
	}

	public function get_dashboard_widget($package_url){
		$widget = new G2_TwigView('widget/dashboard');
		//Retrieve all submission Types
		$submissions_t = Mvc_db::findAll('submission_type');
//		return count($submissions);

		foreach($submissions_t as $key => $sub_type){
//			return Mvc_db::getCell('SELECT COUNT(id) FROM :table WHERE ownSubmission_type_id = :id',array('table' => $sub_type->table,'id' => $sub_type->id));exit;
			$table = $sub_type->table_name;
//			echo $
			$submissions_t[$key]->count = count(Mvc_Db::findAll($table,'ownSubmission_type_id = :id',['id' => $sub_type->id]));

//			$submissions_t[$key]->count = 10;
		}
		$widget->set('submissions',$submissions_t);
		$widget->set('package_url',$package_url);
		return $widget->get_render();
	}
}
