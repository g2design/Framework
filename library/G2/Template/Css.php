<?php
class G2_Template_Css {

	private $base_folder;
	private $base_files;
	private $output_dir;
	private $external_urls;
	
	function default_init($base_dir){
		$this->set_base_folder($base_dir);
	}
	
	function set_base_folder($base){
		$this->base_folder = $base;
	}
	function add_external_url($url){
		$this->external_urls[] = $url;
	}
	function add_base_file($css){
		$this->base_files[] = $this->base_folder.DIRECTORY_SEPARATOR.$css;
	}
	
	function set_output_dir($dir){
		$this->output_dir = $dir;
	}
	
	function render(){
		//Load css files
		//First Load External Sources
		$this->render_external();
		//Load Base CSS Files.
		$this->render_base();
		//Load Special CSS Files
		$this->render_css();
	}
	
	function render_external(){
		foreach($this->external_urls as $ext){ ?>
		<link rel="stylesheet" href="<?php echo $ext ?>">
		<?php }
	}
	
	function render_base(){
		//Move all base css files to accessible static folder.Mark with version number for savety
		foreach($this->base_files as $base){
			if(file_exists($base)){
				//check if new file was made
				$new_file = $this->output_dir.DIRECTORY_SEPARATOR.basename($base);
				if(!file_exists($new_file)){
					//Check if directory exists
					if(!is_dir(dirname($new_file))){
						mkdir(dirname($new_file), 0777, true);
					}
					copy($base, $new_file);
				} else {
					
					//check if new file is newer than original file
					$original_time = filemtime($base);
					$new_file_time = filemtime($new_file);
					if($new_file_time < $original_time){
						//replace with original file if new file is older than original file
						copy($base, $new_file);
					}
				}
			}
			echo "<link rel=\"stylesheet\" href=\"$new_file\" >";
		}
	}
	
	function render_css(){
		
	}
	
}

