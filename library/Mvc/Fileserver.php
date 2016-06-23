<?php

class Mvc_Fileserver extends Mvc_Singleton {

	var $blocked = [], $locations = ['cache'];

	public function add_location($location) {
		$this->locations[] = $location;
	}

	public function serve($file) {
		set_time_limit(0);
		//Process the query
		$extension = Mvc_Functions::get_extension($file);

		if (in_array($extension, $this->blocked)) {
			echo "GET HERE";
			exit;
			$this->_404();
		} else {
			$this->serve_file($this->get_file($file));
		}
	}

	public function exists($file) {
		$extension = Mvc_Functions::get_extension($file);
		if (in_array($extension, $this->blocked)) {
			return false;
		}

		if ($this->get_file($file) !== false) {
			return true;
		} else {
			return false;
		}
	}

	protected function get_file($file) {
		$file_n = false;
		foreach ($this->locations as $location) {
			if (file_exists($location . '/' . $file)) {
				$file_n = $location . '/' . $file;
				break;
			}
		}
		return $file_n;
	}

	/**
	 * Handles all resource request that are not js or css
	 * 
	 * @param type $args
	 * @throws Exception
	 */
	function serve_file($file) {

		if (file_exists($file)) {
			$extension = Mvc_Functions::get_extension($file);
			$func = "{$extension}_mime";
			if (method_exists($this, $func)) {
				$this->$func($file);
			} else {
				$mimepath = '/usr/share/magic'; // may differ depending on your machine
				// try /usr/share/file/magic if it doesn't work
				$mime = finfo_open(FILEINFO_MIME);

				if ($mime === FALSE) {
					throw new Exception('Unable to open finfo');
				}
				$filetype = finfo_file($mime, $file);
				finfo_close($mime);

				if ($filetype === FALSE) {
					throw new Exception('Unable to recognise filetype');
//					$filetype = 'application/octet-stream';
				}


				header("Content-Type: $filetype");
//			header("X-Content-Type-Options: nosniff");
				header("Access-Control-Allow-Origin:*");
				header('Cache-Control:public, max-age=30672000');
			}

			echo file_get_contents($file);
			die();
		} else {
			$this->_404();
			die();
		}
	}

	function block_extension($extensions) {
		if (!is_array($extensions)) {
			$extensions = [$extensions];
		}

		$this->blocked = array_merge($this->blocked, $extensions);
	}

	public function _404() {
		http_response_code(404);
		echo "
			<h1>404 page does not exist</h1>
		";
	}

	function css_mime($file) {
		header("Content-Type: text/css");
		header("X-Content-Type-Options: nosniff");
		header("Access-Control-Allow-Origin:*");
		header('Cache-Control:public, max-age=30672000');

		echo file_get_contents($file);
		die();
	}

}
