<?php

class Meta_Generator {

	var $title, $description;

	/**
	 *
	 * @var Zend_Config_Ini 
	 */
	var $config;

	/**
	 * 
	 * @param Zend_Config_Ini $config
	 */
	public function __construct($config) {

		if (!$config->meta) {
			throw new Exception('Config does not contain a meta group');
		}
		$this->config = $config;
	}

	function get_title($page) {
		// Title Will Consist of the main title if it exists
		$main = $this->config->meta->title->main;
		$seperator = $this->config->meta->title->seperator ? $this->config->meta->title->seperator : ' | ';
		$title = $this->convert_title($page);

		return "$main $seperator $title";
	}

	private function convert_title($page) {
		//Overwrite titles will be store in the meta.title.change.name.subname config area
		$page_config = str_replace('/', '->', filter_var($page, FILTER_SANITIZE_FULL_SPECIAL_CHARS));

		@eval("\$title =  \$this->config->meta->title->change->$page_config;");

		if (@$title) {
			return $title;
		} else {
//			Auto Create title
			$title = '';
			$page_arr = explode('/', $page);
			foreach ($page_arr as $key => $page) {
				$page_arr[$key] = ucfirst(str_replace(array('_', '-'), ' ', $page));
			}
			$seperator = $this->config->meta->title->seperator ? $this->config->meta->title->seperator : ' | ';
			$title = implode($seperator, $page_arr);
			return $title;
		}
	}

	function get_description($page) {
		$default = $this->config->meta->description->index;
		if ($this->convert_description($page)) {
			return $this->convert_description($page);
		} else
			return $default;
	}

	private function convert_description($page) {
		//Overwrite titles will be store in the meta.title.change.name.subname config area
		$page_config = str_replace('/', '->', filter_var($page, FILTER_SANITIZE_FULL_SPECIAL_CHARS));

		@eval("\$description =  \$this->config->meta->description->change->$page_config;");

		return @$description;
	}

	public function get_extra($page) {
		$page_config = str_replace('/', '->', filter_var($page, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		$metas = [];
		if (isset($this->config->meta->add)) {

			foreach ($this->config->meta->add as $key => $config_cont) {
				@eval("\$value =  \$config_cont->$page_config;");


				if (empty($value)) {
					$metas[$key] = $config_cont->index;
				} else {
					$metas[$key] = $value;
				}
			}
		}
		$meta_str = '';
		foreach ($metas as $key => $value) {
			$meta_str .= "<meta name=\"$key\" content=\"$value\" />";
		}

		return $meta_str;
	}

}
