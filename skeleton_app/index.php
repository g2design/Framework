<?php
//Start the Session
session_start();

define('APP_DEPLOY', 'staging');

// Defines
define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('APP_DIR', ROOT_DIR .'application');

require_once '../MVC_Router.php';

$system = MVC_Router::getInstance();
$system->register_package_directory(APP_DIR);
$system->set_default_package('index');
$system->setup_db(ROOT_DIR.'config/database.ini');



$system->dispatch();
