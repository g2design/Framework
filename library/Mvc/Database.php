<?php

class Mvc_Database {
	const SQLITE_SCHEMA = "sqlite";
	const MYSQL_SCHEMA = "mysql";
	private $schema = null;
	private $file = 'databases/sqlite/local.sqlite';
	private $host,$database,$username,$password;
	function __construct() {
		include_once SYSTEM_DIR.'/includes/rb.phar';
	}
	
	function set_schema($schema){
		$this->schema = $schema;
	}
	
	function set_file($file){
		$this->file = $file; 
	}
	
	function set_login_details($host,$database,$username,$password){
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
	}
	
	function connect(){
		switch ($this->schema) :
		case self::SQLITE_SCHEMA :
		default :
			if( !is_dir( dirname( $this->file ) ) ){
				mkdir(dirname($this->file),true);
			}
			if(!file_exists($this->file)) {
				touch($this->file);
			}
			R::setup('sqlite:'.$this->file,'username', 'password');
			break;
		case self::MYSQL_SCHEMA :
			R::setup("mysql:host=$this->host;dbname=$this->database",
			$this->username,$this->password); //mysql or mariaDB
			break;
		endswitch;
		
	}
}