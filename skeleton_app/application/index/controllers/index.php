<?php

class Index_MVC_Controller extends G2_TwigController {

	/**
	 *
	 * @var Meta_Generator
	 */
	var $meta;

	public function __before($params, $action = false) {
		$page = '';
		$page = implode('/', $params);
		if (!$page) {
			$page = 'index';
		}
		if ($action != 'index') {
			$page = $action;
		}

		$config = Package_Index::getInstance()->get_config('meta.ini');

		$this->meta = new Meta_Generator($config);

		$this->template->meta_title = $this->meta->get_title($page);
		$this->template->meta_description = $this->meta->get_description($page);
		$this->template->meta_extra = $this->meta->get_extra($page);
		$this->template->page = $page;
		$this->template->m_gen = $this->meta;
		parent::__before();
	}

	function __construct() {

		parent::__construct();
		$this->template = new G2_TwigTemplate('templates/site_template.twig');
	}

	function index($params) {
		echo 'Hello Developer';
	}

}
