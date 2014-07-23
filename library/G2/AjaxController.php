<?php

class G2_AjaxController extends Controller {

	function _before() {
		echo "Working";
//		global $overwrite_buffer;
//		ob_get_clean();
//		Template::$custom_content = $overwrite_buffer;
		Template::$template_name = 'blank';
//		ob_start();
	}

	function _after() {
//		global $overwrite_buffer;
//		$overwrite_buffer = ob_get_clean();
//		
		echo 'after';
	}

}