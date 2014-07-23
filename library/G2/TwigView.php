<?php

class G2_TwigView extends Mvc_View{
	/**
	 * @var Twig_Environment
	 */
	var $twig,$template;
	var $vars = array();
	public function __construct($template) {
//		echo $this->get_package_uri(true);exit;
		$this->twig = Mvc_Main::getTwig($this->get_package_instance(true));
		$this->template = $template.'.twig';
	}

	function exists(){
		return $this->twig->getLoader()->exists($this->template);
	}

	public function set($var, $val) {
		$this->vars[$var] = $val;
	}
	public function __set($var, $val) {
		$this->vars[$var] = $val;
	}
	public function __get($var) {
		return $this->vars[$var];
	}

	public function render() {
		$template = $this->twig->loadTemplate($this->template);
		echo $template->render($this->vars);
	}
}

