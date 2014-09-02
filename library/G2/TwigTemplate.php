<?php
class G2_TwigTemplate extends Mvc_Base{
	/*
	 * @var Twig_Environment
	 */
	private $twig;
	private $template;
	private $params = array();

	public function __construct($template) {
		if(!Mvc_Functions::get_extension($template)){
			$template.= '.'.Mvc_Main::get_twig_extension($this->get_package_instance(true));
		}

		$this->template = $template;
		$this->params['base_url'] = BASE_URL;

		$this->twig = Mvc_Main::getTwig($this->get_package_instance(true));
	}

	public function set_template_file($file){
	    $this->template = $file;
	}

	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	public function __get($name) {
		if(isset($this->params[$name]))
		return $this->params[$name];
	}

	/**
	 *
	 * @param type $content
	 * @param type $variables
	 */
	public function render($content){
		$template = $this->twig->loadTemplate($this->template);
		$this->params['content'] = $content;
		echo $template->render($this->params);
	}
}