<?php

class G2_Storage_File extends Mvc_Base{
	protected $name,$uri;
	
	public function __construct($file_uri,$filename) {
		$this->name = $filename;
		$this->uri = $file_uri;
		if(!file_exists($this->uri)){
			throw new Exception("File does not exist");
		}
	}
	
	function get_name(){
		return $this->name;
	}
	
	function get_uri(){
		return $this->uri;
	}
	
	/**
	 * Will move a file from its current location to a new Location;
	 * @param type $new_location
	 */
	function move($new_location){
		$new_name = basename($new_location);
		$new_directory = dirname($new_location);
		
		//Create the directory where the file will be stored
		$new_directory = $this->create_directory($new_directory);
		
		$new_uri = $new_directory.DIRECTORY_SEPARATOR.$this->clean_name($new_name);
		rename($this->uri, $new_uri);
		
		$this->name = $new_name;
		$this->uri = $new_uri;
	}
	
	function delete(){
		unlink($this->uri);
	}
	
	function get_extension(){
		$str = $this->name;
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
	
	private function create_directory($dir){
		//Validate Directory
			//Clean the directory name
		
		if(!is_dir($dir)){
			mkdir($dir,'0777',true);
		}
		return $dir;
	}
	/**
	 * 
	 * @param type $name
	 * @return type
	 */
	public function clean_name($name){
		return trim(preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $name));
	}
}

