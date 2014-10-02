<?php

class Admin_Mvc_Controller extends G2_TwigController {

	function index() {
		//Get all Submission Types
		$view = new G2_TwigView('pages/index');

		//Retrieve all submission Types
		$submissions = Mvc_Db::findAll('submission_type');
		foreach ($submissions as $key => $sub_type) {
			$table = $sub_type->table_name;
			$submissions[$key]->count = count(Mvc_Db::findAll($table,'ownSubmission_type_id = :id',['id' => $sub_type->id]));
		}
		$view->set('submissions', $submissions);
		$view->set('package_url', PACKAGE_URL);
		$view->render();
	}

	function __construct() {
		parent::__construct();
		$this->template = new G2_TwigTemplate('templates/submissions.twig');
		$this->template->user = G()->get_user();
		$this->template->package_url = PACKAGE_URL;
	}

	function edit() {
		echo "works";
	}

	function view($args) {
		if(isset($_GET['p'])){
			$page_num = $_GET['p'];
		} else $page_num = 1;
		$id = array_shift($args);
		$sub = R::findOne('submission_type', 'id = :id', ['id' => $id]);
		if ($sub) {
			$table = $sub->table_name;
			$submissions = R::findAll($table,'ORDER BY id DESC');
			$submissions = $this->filter_out($submissions, ['id', 'ownSubmission_type_id']);
//			var_dump($submissions);exit;
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($submissions));
			$paginator->setDefaultItemCountPerPage(10);
			
			$page_count = ceil($paginator->getTotalItemCount() / 10);
			$paginator->setCurrentPageNumber($page_num);
//
//
			$submissions = $paginator->getCurrentItems();
			$view = new G2_TwigView('pages/submissions');
			$view->set('submissions', $submissions);
			$view->set('sub_type', $sub);
			$view->set('page_count',$page_count);
			$view->set('sub_head', current($submissions));
			$view->set('current_url', PACKAGE_URL."view/$id/");
			$view->set('current',$page_num);
			$view->render();
		}
	}

	private function filter_out($objects, $keys) {
		foreach ($objects as $key => $object) {
			foreach ($keys as $key_r) {
				unset($objects[$key]->$key_r);
			}
		}
		return $objects;
	}

}
