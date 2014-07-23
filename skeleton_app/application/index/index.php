<?php

class Package_Index extends Mvc_Package {
	
	public function __construct() {
		MVC_Router::getInstance()->add_library($this->get_package_dir().'classes');
	}
	
	public function get_label() {
		
		return str_replace('_', ' ', strtolower(__CLASS__));
	}

	public function get_action() {
		return strtolower(__CLASS__);
	}
	
	/*public function route($controller = 'index', $action = 'index', $params = array()) {
		$cache = Mvc_Main::get_cache_object();
		$cache_id = md5("$controller-$action-".implode('--',$params));
		$content = $cache->load($cache_id);
		if($content && empty($_POST) && !isset($_GET['reload'])){
			return $content;
		} else {
			$content = parent::route($controller, $action, $params);
			$content = Minify_HTML::minify($content);
			
			if(empty($_POST)){
				
				
				$cache->save($content, $cache_id);
			}
			
			$this->compress_css();
			$this->compress_js();
			return $content;
		}
		
	}*/
	
	public function compress_css(){
//		$css = new Ext_Csstidy();

		


//		$css->set_cfg('remove_last_;',TRUE);
		// Css files from folder static/css
		$files = Mvc_Functions::directoryToArray(ROOT_DIR.'static/css', true);
		foreach($files as $file){
			if(in_array($file, array('..','.')) || !Mvc_Functions::endsWith($file, '.css') || strpos($file, 'min-') !== false){
				continue;
			}
			$directory = dirname($file);
			$new_file = "min-".basename($file);
			$min_location = $directory.'/'.$new_file;
			if(file_exists($directory.'/'.$new_file) && filemtime($file) < filemtime($min_location)){
				continue;
			} else {
				$min_css = Mvc_Functions::compress(file_get_contents($file));
				
				file_put_contents($min_location, $min_css);
			}
		}
			
//		echo @$css->print->formatted();exit;
//		@$css->parse('.test{ display:none; }');
	}
	
	function compress_js(){
		$files = Mvc_Functions::directoryToArray(ROOT_DIR.'static/js', true);
		foreach($files as $file){
			if(in_array($file, array('..','.')) || !Mvc_Functions::endsWith($file, '.js') || strpos($file, 'min-') !== false){
				continue;
			}
			$directory = dirname($file);
			$new_file = "min-".basename($file);
			$min_location = $directory.'/'.$new_file;
			if(file_exists($directory.'/'.$new_file) && filemtime($file) < filemtime($min_location)){
				continue;
			} else {
				$min_js = JSMin::minify(file_get_contents($file));
				
				file_put_contents($min_location, $min_js);
			}
		}
	}

}
